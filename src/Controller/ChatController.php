<?php

namespace App\Controller;

use App\Entity\Chatbox;
use App\Entity\Message;
use App\Enum\MessageType;
use App\Entity\Personne;
use App\Service\ChatAIService;
use App\Service\ChatWebSocketService;
use App\Service\MercureJwtProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/chat')]
class ChatController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ChatWebSocketService $webSocketService,
        private ChatAIService $aiService,
        private MercureJwtProvider $jwtProvider
    ) {}

    #[Route('/{id}', name: 'chat_view', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function view(Chatbox $chatbox): Response
    {
        if (!$this->isGranted('view', $chatbox)) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette conversation.');
        }

        $mercureUrl = $_ENV['MERCURE_PUBLIC_URL'] ?? 'http://localhost:3000/.well-known/mercure';

        return $this->render('chat/view.html.twig', [
            'chatbox' => $chatbox,
            'mercureUrl' => $mercureUrl,
            'subscriberToken' => $this->jwtProvider->getSubscriberToken()
        ]);
    }

    #[Route('/{id}/messages', name: 'chat_messages', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getMessages(Chatbox $chatbox, Request $request): JsonResponse
    {
        if (!$this->isGranted('view', $chatbox)) {
            throw $this->createAccessDeniedException();
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

    #[Route('/{id}/send', name: 'chat_send', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function sendMessage(Request $request, Chatbox $chatbox): JsonResponse
    {
        error_log("=== Début de la méthode sendMessage ===");
        error_log("URI de la requête : " . $request->getRequestUri());
        error_log("Méthode : " . $request->getMethod());
        error_log("Headers : " . json_encode($request->headers->all()));
        
        if (!$this->isGranted('send_message', $chatbox)) {
            error_log("Accès refusé à la chatbox");
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('chat', $request->headers->get('X-CSRF-TOKEN'))) {
            error_log("Token CSRF invalide");
            error_log("Token reçu : " . $request->headers->get('X-CSRF-TOKEN'));
            return $this->json(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $content = json_decode($request->getContent(), true);
        error_log("Contenu reçu : " . json_encode($content));
        
        if (empty($content['message'])) {
            error_log("Message vide");
            return $this->json(['error' => 'Message cannot be empty'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Personne $user */
        $user = $this->getUser();
        error_log("Utilisateur identifié: " . $user->getId() . " (" . $user->getEmail() . ")");
        
        // Créer et sauvegarder le message utilisateur
        $message = new Message();
        $message->setChatbox($chatbox)
                ->setMessage($content['message'])
                ->setMessageType(MessageType::USER)
                ->setTimestamp(new \DateTime())
                ->setSenderId($user->getId());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        error_log("Message utilisateur sauvegardé avec l'ID: " . $message->getId());

        // Publier le message via WebSocket
        try {
            error_log("Publication du message utilisateur via WebSocket");
            $this->webSocketService->publishNewMessage($message);
            error_log("Message utilisateur publié avec succès");
        } catch (\Exception $e) {
            error_log("Erreur WebSocket: " . $e->getMessage());
            error_log("Trace WebSocket: " . $e->getTraceAsString());
        }

        // Si la chatbox a une IA associée, générer une réponse de manière asynchrone
        if ($chatbox->getIa()) {
            error_log("IA trouvée, génération de la réponse en arrière-plan");
            try {
                $this->aiService->handleUserMessage($message);
            } catch (\Exception $e) {
                error_log("Erreur lors de la génération de la réponse IA: " . $e->getMessage());
                error_log("Trace: " . $e->getTraceAsString());
            }
        }

        error_log("=== Fin de la méthode sendMessage ===");

        return $this->json([
            'id' => $message->getId(),
            'content' => $message->getMessage(),
            'messageType' => $message->getMessageType()->value,
            'senderId' => $message->getSenderId(),
            'timestamp' => $message->getTimestamp()->format('c'),
            'reactions' => [],
            'isRead' => false
        ]);
    }

    #[Route('/{id}/react', name: 'chat_react', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addReaction(Message $message, Request $request): JsonResponse
    {
        if (!$this->isGranted('view', $message->getChatbox())) {
            throw $this->createAccessDeniedException();
        }

        $content = json_decode($request->getContent(), true);
        
        if (empty($content['emoji'])) {
            return $this->json(['error' => 'Emoji cannot be empty'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Personne $user */
        $user = $this->getUser();
        $message->addReaction($content['emoji'], $user->getId());
        
        $this->entityManager->flush();

        $this->webSocketService->publishReactionUpdate($message);

        return $this->json(['status' => 'ok']);
    }

    #[Route('/{id}/read', name: 'chat_mark_read', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function markAsRead(Message $message): JsonResponse
    {
        if (!$this->isGranted('view', $message->getChatbox())) {
            throw $this->createAccessDeniedException();
        }

        $message->setIsRead(true);
        $this->entityManager->flush();

        $this->webSocketService->publishReadStatus($message);

        return $this->json(['status' => 'ok']);
    }

    #[Route('/{id}/typing', name: 'chat_typing', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function setTypingStatus(Chatbox $chatbox, Request $request): JsonResponse
    {
        if (!$this->isGranted('view', $chatbox)) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('chat', $request->headers->get('X-CSRF-TOKEN'))) {
            return $this->json(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $content = json_decode($request->getContent(), true);
        $isTyping = $content['typing'] ?? false;

        /** @var Personne $user */
        $user = $this->getUser();
        
        try {
            $this->webSocketService->publishTypingEvent(
                $chatbox,
                $user->getId(),
                $isTyping
            );

            return $this->json(['status' => 'ok']);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Erreur lors de la mise à jour du statut de frappe'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}