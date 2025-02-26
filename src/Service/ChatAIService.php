<?php

namespace App\Service;

use App\Entity\IA;
use App\Entity\Message;
use App\Entity\Chatbox;
use App\Enum\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

class ChatAIService
{
    private const MAX_TOKENS = 150; // Limite pour le service gratuit
    private const REQUEST_TIMEOUT = 10; // Timeout en secondes
    private const MAX_RETRIES = 2;
    private const RATE_LIMIT_DELAY = 20; // Délai en secondes pour la limite de taux

    // Codes d'erreur OpenAI
    private const ERROR_UNAUTHORIZED = 401;
    private const ERROR_RATE_LIMIT = 429;
    private const ERROR_SERVER = 500;

    private $httpClient;
    private $entityManager;
    private $webSocketService;
    private $params;
    private $logger;

    public function __construct(
        HttpClientInterface $httpClient,
        EntityManagerInterface $entityManager,
        ChatWebSocketService $webSocketService,
        ParameterBagInterface $params,
        LoggerInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->webSocketService = $webSocketService;
        $this->params = $params;
        $this->logger = $logger;
    }

    /**
     * Génère une réponse AI pour un message donné
     */
    public function generateResponse(Message $userMessage): ?Message
    {
        $chatbox = $userMessage->getChatbox();
        $ia = $chatbox->getIa();

        try {
            $this->logger->info("=== Début de la génération de réponse IA ===");
            $this->logger->info("Message utilisateur: " . $userMessage->getMessage());
            
            $content = $this->callOpenAI($userMessage, $ia);
            
            if ($content) {
                $this->logger->info("Réponse OpenAI reçue: " . $content);
                
                $aiMessage = new Message();
                $aiMessage->setChatbox($chatbox)
                        ->setMessage($content)
                        ->setMessageType(MessageType::AI)
                        ->setTimestamp(new \DateTime())
                        ->setSenderId($ia->getId());

                $this->logger->info("Message IA créé avec l'ID: " . $aiMessage->getId());
                
                try {
                    $this->logger->info("Publication du message via WebSocket...");
                    $this->webSocketService->publishNewMessage($aiMessage);
                    $this->logger->info("Message publié avec succès via WebSocket");
                } catch (\Exception $e) {
                    $this->logger->error("Erreur lors de la publication WebSocket: " . $e->getMessage());
                    $this->logger->error("Trace WebSocket: " . $e->getTraceAsString());
                }
                
                return $aiMessage;
            }
            
            return null;
        } catch (\Exception $e) {
            $this->logger->error("Erreur lors de la génération de la réponse: " . $e->getMessage());
            $this->logger->error("Trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Traite un message utilisateur et génère une réponse IA
     */
    public function handleUserMessage(Message $userMessage): void
    {
        $this->logger->info("=== Début du traitement du message utilisateur ===");
        $this->logger->info("Message ID: " . $userMessage->getId());
        $this->logger->info("Contenu du message: " . $userMessage->getMessage());
        
        $chatbox = $userMessage->getChatbox();
        $this->logger->info("Chatbox ID: " . $chatbox->getId());
        
        $ia = $chatbox->getIa();

        if (!$ia) {
            $this->logger->error("Pas d'IA associée à la chatbox");
            throw new \RuntimeException('Aucune IA associée à cette chatbox');
        }

        $this->logger->info("IA trouvée: " . $ia->getId());
        $this->logger->info("Nom de l'IA: " . $ia->getNom());
        $this->logger->info("Modèle: " . $ia->getModel());
        $this->logger->info("Clé API: " . substr($ia->getApiKey(), 0, 10) . "...");

        // Vérifier si le message nécessite une réponse de l'IA
        if ($userMessage->getMessageType() !== MessageType::USER) {
            $this->logger->info("Message non utilisateur, ignoré");
            return;
        }

        try {
            $this->logger->info("Génération de la réponse IA...");
            $aiMessage = $this->generateResponse($userMessage);
            
            if ($aiMessage) {
                $this->logger->info("Réponse IA générée avec succès: " . $aiMessage->getMessage());
                $this->entityManager->persist($aiMessage);
                $this->entityManager->flush();
                
                // Publier le message via WebSocket
                $this->logger->info("Publication de la réponse via WebSocket");
                $this->webSocketService->publishNewMessage($aiMessage);
                $this->logger->info("Réponse publiée avec succès");
            } else {
                $this->logger->warning("Aucune réponse générée par l'IA");
            }
        } catch (\Exception $e) {
            $this->logger->error("Erreur lors de la génération de la réponse IA: " . $e->getMessage());
            $this->logger->error("Trace complète: " . $e->getTraceAsString());
            throw $e;
        }
        
        $this->logger->info("=== Fin du traitement du message utilisateur ===");
    }

    private function handleOpenAIError(int $statusCode, array $errorData): void
    {
        $errorMessage = $errorData['error']['message'] ?? 'Unknown error';
        
        switch ($statusCode) {
            case self::ERROR_UNAUTHORIZED:
                throw new \RuntimeException("Erreur d'authentification OpenAI: " . $errorMessage);
            case self::ERROR_RATE_LIMIT:
                throw new \RuntimeException("Limite de taux OpenAI atteinte: " . $errorMessage);
            case self::ERROR_SERVER:
                throw new \RuntimeException("Erreur serveur OpenAI: " . $errorMessage);
            default:
                throw new \RuntimeException("Erreur OpenAI ($statusCode): " . $errorMessage);
        }
    }

    private function validateApiKey(string $apiKey): void
    {
        if (empty($apiKey) || $apiKey === 'your_api_key_here') {
            throw new \RuntimeException('Clé API OpenAI non configurée ou invalide');
        }
    }

    private function buildOpenAIRequest(Message $userMessage, IA $ia): array
    {
        $context = $this->buildConversationContext($userMessage->getChatbox());
        
        return [
            'model' => $ia->getModel() ?: 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $ia->getDefaultContext() ?: 'Tu es un assistant helpdesk professionnel.'],
                ...array_map(
                    fn($msg) => [
                        'role' => $msg->getMessageType() === MessageType::AI ? 'assistant' : 'user',
                        'content' => $msg->getMessage()
                    ],
                    $context
                ),
                ['role' => 'user', 'content' => $userMessage->getMessage()]
            ],
            'max_tokens' => self::MAX_TOKENS,
            'temperature' => $ia->getTemperature(),
        ];
    }

    /**
     * Appelle l'API OpenAI avec gestion des limites et des erreurs
     */
    private function callOpenAI(Message $userMessage, IA $ia): ?string
    {
        $retryCount = 0;
        $lastError = null;

        $this->logger->info("=== Début de l'appel à OpenAI ===");
        $this->logger->info("Message ID: " . $userMessage->getId());
        $this->logger->info("IA ID: " . $ia->getId());
        $this->logger->info("Modèle: " . $ia->getModel());
        $this->logger->info("Température: " . $ia->getTemperature());

        do {
            try {
                $this->logger->info("Tentative d'appel à OpenAI (essai " . ($retryCount + 1) . "/" . self::MAX_RETRIES . ")");
                
                $apiKey = $ia->getApiKey() ?: $this->params->get('OPENAI_API_KEY');
                $this->logger->info("Clé API: " . substr($apiKey, 0, 10) . "...");
                $this->validateApiKey($apiKey);

                $requestData = $this->buildOpenAIRequest($userMessage, $ia);
                $this->logger->info("Configuration de la requête OpenAI: " . json_encode($requestData));

                $this->logger->info("Envoi de la requête à OpenAI...");
                $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $requestData,
                    'timeout' => self::REQUEST_TIMEOUT
                ]);

                $statusCode = $response->getStatusCode();
                $this->logger->info("Code de statut HTTP: " . $statusCode);

                if ($statusCode !== 200) {
                    $errorData = $response->toArray(false);
                    $this->logger->error("Erreur OpenAI: " . json_encode($errorData));
                    $this->handleOpenAIError($statusCode, $errorData);
                }

                $data = $response->toArray();
                $this->logger->info("Réponse reçue de OpenAI: " . json_encode($data));

                if (!isset($data['choices'][0]['message']['content'])) {
                    $this->logger->error("Format de réponse OpenAI invalide: " . json_encode($data));
                    throw new \RuntimeException("Format de réponse OpenAI invalide");
                }

                $content = $data['choices'][0]['message']['content'];
                $this->logger->info("Contenu de la réponse: " . $content);
                $this->logger->info("=== Fin de l'appel à OpenAI avec succès ===");

                return $content;

            } catch (\Exception $e) {
                $this->logger->error("Erreur lors de l'appel à OpenAI: " . $e->getMessage());
                $this->logger->error("Trace: " . $e->getTraceAsString());
                $lastError = $e;
                $retryCount++;

                if (str_contains($e->getMessage(), 'rate limit')) {
                    $this->logger->info("Rate limit atteint, attente de " . self::RATE_LIMIT_DELAY . " secondes");
                    sleep(self::RATE_LIMIT_DELAY);
                } elseif (str_contains($e->getMessage(), 'timeout')) {
                    $this->logger->info("Timeout, attente de 5 secondes");
                    sleep(5);
                } else {
                    $this->logger->info("Erreur générique, attente de 1 seconde");
                    sleep(1);
                }
            }
        } while ($retryCount < self::MAX_RETRIES);

        $this->logger->info("=== Fin de l'appel à OpenAI avec échec après " . $retryCount . " tentatives ===");

        if ($lastError) {
            throw $lastError;
        }

        return null;
    }

    /**
     * Construit le contexte de la conversation en récupérant les derniers messages
     */
    private function buildConversationContext(Chatbox $chatbox): array
    {
        return $this->entityManager
            ->getRepository(Message::class)
            ->createQueryBuilder('m')
            ->where('m.chatbox = :chatbox')
            ->setParameter('chatbox', $chatbox)
            ->orderBy('m.timestamp', 'DESC')
            ->setMaxResults(5) // Limite pour le contexte gratuit
            ->getQuery()
            ->getResult();
    }
}
