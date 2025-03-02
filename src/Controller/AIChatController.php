<?php

namespace App\Controller;

use App\Entity\Chatbox;
use App\Entity\IA;
use App\Entity\Message;
use App\Entity\Personne;
use App\Enum\MessageType;
use App\Service\ChatAIService;
use App\Service\MercureJwtProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/aide-ia')]
class AIChatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChatAIService $aiService,
        private MercureJwtProvider $jwtProvider
    ) {}

    #[Route('/', name: 'app_ai_chat', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        // Vérifier si l'utilisateur est connecté
        /** @var Personne|null $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour utiliser cette fonctionnalité.');
            return $this->redirectToRoute('app_login');
        }

        // Créer une chatbox temporaire pour l'assistance IA
        $chatbox = new Chatbox();
        $chatbox->setCreatedAt(new \DateTime());
        $chatbox->setIsTemporary(true);
        
        // Créer et associer une nouvelle IA à la chatbox
        $ia = $this->createAI("Assistant IA");
        if ($ia) {
            $chatbox->setIa($ia);
            error_log('IA créée avec succès et associée à la chatbox: ' . $ia->getId());
        } else {
            $this->addFlash('warning', 'Impossible de créer une IA pour l\'assistance. Vous pouvez directement créer un ticket.');
            error_log('Échec de création de l\'IA');
            return $this->redirectToRoute('app_ticket_new');
        }
        
        // Sauvegarder la chatbox
        $this->entityManager->persist($chatbox);
        $this->entityManager->flush();
        
        // Envoyer un message de bienvenue
        $this->sendWelcomeMessage($chatbox);
        
        $mercureUrl = $_ENV['MERCURE_PUBLIC_URL'] ?? 'http://localhost:3000/.well-known/mercure';
        $subscriberToken = $this->jwtProvider->getSubscriberToken();
        
        return $this->render('ai_chat/index.html.twig', [
            'chatbox' => $chatbox,
            'mercureUrl' => $mercureUrl,
            'subscriberToken' => $subscriberToken
        ]);
    }
    
    #[Route('/{id}/send', name: 'app_ai_chat_send', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function sendMessage(Request $request, Chatbox $chatbox): JsonResponse
    {
        if (!$chatbox->getIa() || !$chatbox->isTemporary()) {
            return $this->json(['error' => 'Chatbox invalide'], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->isCsrfTokenValid('ai_chat', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Token CSRF invalide'], Response::HTTP_FORBIDDEN);
        }

        $content = json_decode($request->getContent(), true);
        
        if (empty($content['message'])) {
            return $this->json(['error' => 'Le message ne peut pas être vide'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Personne $user */
        $user = $this->getUser();
        
        // Vérifier que l'utilisateur est bien connecté
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }
        
        try {
            // Créer et sauvegarder le message utilisateur
            $message = new Message();
            $message->setChatbox($chatbox)
                    ->setMessage($content['message'])
                    ->setMessageType(MessageType::USER)
                    ->setTimestamp(new \DateTime())
                    ->setSenderId($user->getId());

            $this->entityManager->persist($message);
            $this->entityManager->flush();
            
            // Préparer la réponse pour le message utilisateur
            $response = [
                'id' => $message->getId(),
                'content' => $message->getMessage(),
                'messageType' => $message->getMessageType()->value,
                'senderId' => $message->getSenderId(),
                'timestamp' => $message->getTimestamp()->format('c'),
                'reactions' => [],
                'isRead' => false
            ];
            
            // Générer une réponse IA de manière asynchrone en utilisant un thread séparé
            // pour ne pas bloquer la réponse au client
            $this->aiService->handleUserMessage($message);

            return $this->json($response);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur lors de l\'envoi du message',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    #[Route('/{id}/messages', name: 'app_ai_chat_messages', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getMessages(Chatbox $chatbox, Request $request): JsonResponse
    {
        if (!$chatbox->isTemporary()) {
            return $this->json(['error' => 'Chatbox invalide'], Response::HTTP_BAD_REQUEST);
        }

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 50);

        $messages = $this->entityManager
            ->getRepository(Message::class)
            ->createQueryBuilder('m')
            ->where('m.chatbox = :chatbox')
            ->setParameter('chatbox', $chatbox)
            ->orderBy('m.timestamp', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $this->json([
            'messages' => array_map(fn(Message $message) => [
                'id' => $message->getId(),
                'content' => $message->getMessage(),
                'messageType' => $message->getMessageType()->value,
                'senderId' => $message->getSenderId(),
                'timestamp' => $message->getTimestamp()->format('c'),
                'reactions' => $message->getReactions(),
                'isRead' => $message->isRead()
            ], array_reverse($messages))
        ]);
    }
    
    #[Route('/{id}/create-ticket', name: 'app_ai_chat_create_ticket', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function createTicket(Chatbox $chatbox, Request $request): Response
    {
        if (!$chatbox->isTemporary()) {
            $this->addFlash('error', 'Session de chat invalide.');
            return $this->redirectToRoute('app_ticket_new');
        }
        
        // Stocker l'ID de la chatbox dans la session pour pouvoir récupérer les messages plus tard
        $request->getSession()->set('ai_chat_id', $chatbox->getId());
        
        return $this->redirectToRoute('app_ticket_new');
    }
    
    /**
     * Endpoint pour gérer les événements de frappe
     */
    #[Route('/{id}/typing', name: 'app_ai_chat_typing', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function handleTyping(Request $request, Chatbox $chatbox): JsonResponse
    {
        if (!$chatbox->isTemporary()) {
            return $this->json(['error' => 'Chatbox invalide'], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->isCsrfTokenValid('ai_chat', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Token CSRF invalide'], Response::HTTP_FORBIDDEN);
        }

        /** @var Personne $user */
        $user = $this->getUser();
        
        // Vérifier que l'utilisateur est bien connecté
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }
        
        try {
            $content = json_decode($request->getContent(), true);
            $isTyping = $content['isTyping'] ?? false;
            
            // Ici, vous pourriez publier l'événement de frappe via Mercure
            // Pour l'instant, nous retournons simplement un succès
            
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur lors du traitement de l\'événement de frappe',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Crée une nouvelle instance d'IA et la persiste en base de données
     * 
     * @param string $name Nom de l'IA
     * @return IA|null L'entité IA créée ou null en cas d'erreur
     */
    private function createAI(string $name): ?IA
    {
        try {
            // Récupérer la clé API depuis les variables d'environnement
            $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
            
            if (!$apiKey) {
                throw new \RuntimeException('Clé API OpenAI non configurée');
            }
            
            $ia = new IA();
            $ia->setNom($name)
               ->setApiKey($apiKey)
               ->setModel('gpt-3.5-turbo')
               ->setTemperature(0.7)
               ->setDefaultContext('Je suis un assistant helpdesk qui aide les utilisateurs avec leurs problèmes techniques.');
            
            $this->entityManager->persist($ia);
            $this->entityManager->flush();
            
            return $ia;
        } catch (\Exception $e) {
            // Log l'erreur
            error_log('Erreur lors de la création de l\'IA: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Envoie un message de bienvenue à l'utilisateur lorsqu'il démarre une conversation avec l'IA.
     *
     * @param Chatbox $chatbox La chatbox dans laquelle le message doit être envoyé.
     *
     * @throws \RuntimeException Si aucune IA n'est associée à la chatbox.
     */
    private function sendWelcomeMessage(Chatbox $chatbox): void
    {
        /** @var IA|null $ia */
        $ia = $chatbox->getIa();
        if (!$ia) {
            throw new \RuntimeException('Aucune IA associée à cette chatbox');
        }
        
        $message = new Message();
        $message->setChatbox($chatbox)
                ->setMessage("Bonjour ! Je suis votre assistant virtuel. Comment puis-je vous aider aujourd'hui ? Décrivez votre problème et je ferai de mon mieux pour le résoudre. Si je ne parviens pas à vous aider, vous pourrez créer un ticket d'assistance.")
                ->setMessageType(MessageType::AI)
                ->setTimestamp(new \DateTime())
                ->setSenderId($ia->getId());
                
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }
}
