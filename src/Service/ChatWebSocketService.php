<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Chatbox;
use App\Entity\Personne;
use App\Enum\MessageType;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

class ChatWebSocketService
{
    private HubInterface $hub;
    private SerializerInterface $serializer;
    private string $mercurePublicUrl;
    private LoggerInterface $logger;

    public function __construct(
        HubInterface $hub,
        SerializerInterface $serializer,
        string $mercurePublicUrl,
        LoggerInterface $logger
    ) {
        $this->hub = $hub;
        $this->serializer = $serializer;
        $this->mercurePublicUrl = $mercurePublicUrl;
        $this->logger = $logger;
    }

    private function getMercureHubUrl(): string
    {
        $url = $this->mercurePublicUrl ?? 'http://localhost:3000/.well-known/mercure';
        $this->logger->info("URL du hub Mercure : " . $url);
        return $url;
    }

    public function publish(Message $message): bool
    {
        try {
            $chatboxId = $message->getChatbox()->getId();
            $topic = "/chat/{$chatboxId}";
            
            $data = [
                'id' => $message->getId(),
                'content' => $message->getMessage(),
                'messageType' => $message->getMessageType()->value,
                'senderId' => $message->getSenderId(),
                'timestamp' => $message->getTimestamp()->format('Y-m-d\TH:i:s.v\Z'),
                'type' => 'message' // Ajouter un type pour différencier des autres événements
            ];
            
            if ($message->getMessageType()->value === 'ai' && $message->getUserMessageId()) {
                $data['userMessageId'] = $message->getUserMessageId();
            }
            
            $update = new Update(
                $topic,
                json_encode($data),
                true  // Private = true pour forcer l'authentification
            );
            
            $this->logger->info('Publication WebSocket', [
                'topic' => $topic,
                'data' => $data,
                'private' => true
            ]);
            
            $this->hub->publish($update);
            
            $this->logger->info('Message publié avec succès');
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la publication WebSocket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function publishNewMessage(Message $message): void
    {
        $this->logger->info("=== Début de la publication du message ===", [
            'messageId' => $message->getId(),
            'chatboxId' => $message->getChatbox()->getId()
        ]);
        
        // Utiliser la méthode publish
        $success = $this->publish($message);
        
        $this->logger->info("=== Fin de la publication du message ===", [
            'success' => $success
        ]);
    }

    /**
     * Publie des données directement sur un topic Mercure
     * 
     * @param array $data Les données à publier
     * @param string $topic Le topic sur lequel publier
     * @return bool Succès ou échec de la publication
     */
    public function publishMessage(array $data, string $topic): bool
    {
        try {
            $this->logger->info("=== Début de la publication des données ===", [
                'topic' => $topic,
                'dataKeys' => array_keys($data)
            ]);
            
            // Ajouter un type par défaut si non spécifié
            if (!isset($data['type'])) {
                $data['type'] = 'message';
            }
            
            // S'assurer que conversationId est présent pour les messages
            if ($data['type'] === 'message' && !isset($data['conversationId']) && isset($data['message'])) {
                // Essayer d'extraire l'ID de conversation du topic
                if (preg_match('/\/chat\/(\d+)/', $topic, $matches)) {
                    $data['conversationId'] = (int)$matches[1];
                    $this->logger->info("ID de conversation extrait du topic: " . $data['conversationId']);
                }
            }
            
            // Convertir les données en JSON
            $jsonData = json_encode($data);
            $this->logger->info("Données JSON à publier: " . $jsonData);
            
            // Créer l'objet Update pour Mercure
            $update = new Update(
                $topic,
                $jsonData,
                true  // Private = true pour forcer l'authentification
            );
            
            $this->logger->info('Publication WebSocket', [
                'topic' => $topic,
                'data' => $data,
                'private' => true
            ]);
            
            // Publier l'update via le hub Mercure
            $id = $this->hub->publish($update);
            $this->logger->info('Données publiées avec succès, ID: ' . $id);
            
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la publication WebSocket', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Publie une mise à jour des réactions d'un message
     */
    public function publishReactionUpdate(Message $message): void
    {
        $chatboxId = $message->getChatbox()->getId();
        $topic = "/chat/{$chatboxId}";
        
        try {
            $data = [
                'type' => 'reaction',
                'messageId' => $message->getId(),
                'reactions' => $message->getReactions()
            ];

            $update = new Update(
                $topic,
                json_encode($data),
                true
            );

            $this->logger->info('Publication des réactions', [
                'topic' => $topic,
                'messageId' => $message->getId()
            ]);
            
            $this->hub->publish($update);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la publication des réactions', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Publie une mise à jour du statut de lecture d'un message
     */
    public function publishReadStatus(Message $message): void
    {
        $chatboxId = $message->getChatbox()->getId();
        $topic = "/chat/{$chatboxId}";
        
        try {
            $data = [
                'type' => 'read_status',
                'messageId' => $message->getId(),
                'isRead' => $message->isRead()
            ];

            $update = new Update(
                $topic,
                json_encode($data),
                true
            );

            $this->logger->info('Publication du statut de lecture', [
                'topic' => $topic,
                'messageId' => $message->getId()
            ]);
            
            $this->hub->publish($update);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la publication du statut de lecture', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Publie un événement de frappe (typing)
     */
    public function publishTypingStatus(Chatbox $chatbox, ?Personne $user, bool $isTyping): void
    {
        try {
            $chatboxId = $chatbox->getId();
            $topic = "/chat/{$chatboxId}";
            
            $data = [
                'type' => 'typing',
                'userId' => $user ? $user->getId() : null,
                'isTyping' => $isTyping,
                'timestamp' => (new \DateTime())->format('Y-m-d\TH:i:s.v\Z')
            ];
            
            $update = new Update(
                $topic,
                json_encode($data),
                true  // Private = true pour forcer l'authentification
            );
            
            $this->logger->info('Publication statut de frappe', [
                'topic' => $topic,
                'data' => $data
            ]);
            
            $this->hub->publish($update);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la publication du statut de frappe', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
