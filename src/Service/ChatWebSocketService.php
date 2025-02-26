<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Chatbox;
use App\Enum\MessageType;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

class ChatWebSocketService
{
    private HubInterface $hub;
    private SerializerInterface $serializer;
    private string $mercurePublicUrl;

    public function __construct(
        HubInterface $hub,
        SerializerInterface $serializer,
        string $mercurePublicUrl
    ) {
        $this->hub = $hub;
        $this->serializer = $serializer;
        $this->mercurePublicUrl = $mercurePublicUrl;
    }

    private function getMercureHubUrl(): string
    {
        $url = $_ENV['MERCURE_PUBLIC_URL'] ?? 'http://localhost:3000/.well-known/mercure';
        error_log("URL du hub Mercure : " . $url);
        return $url;
    }

    public function publishNewMessage(Message $message): void
    {
        error_log("=== Début de la publication du message ===");
        error_log("Message ID: " . $message->getId());
        error_log("Topic: /chat/" . $message->getChatbox()->getId());

        try {
            $data = [
                'id' => $message->getId(),
                'content' => $message->getMessage(),
                'messageType' => $message->getMessageType()->value,
                'senderId' => $message->getSenderId(),
                'timestamp' => $message->getTimestamp()->format('c'),
                'type' => 'message',
                'reactions' => [],
                'isRead' => false
            ];

            error_log("Données à publier : " . json_encode($data));

            $update = new Update(
                [sprintf('/chat/%d', $message->getChatbox()->getId())],
                json_encode($data),
                true
            );

            error_log("Topics : " . implode(', ', $update->getTopics()));
            error_log("Données de l'update : " . $update->getData());

            // Publication du message
            $result = $this->hub->publish($update);
            error_log("Résultat de la publication : " . ($result ? "succès" : "échec"));

            // Attendre un court instant pour s'assurer que le message est publié
            usleep(100000); // 100ms

            error_log("Message publié avec succès");
        } catch (\Exception $e) {
            error_log("Erreur lors de la publication : " . $e->getMessage());
            error_log("Trace : " . $e->getTraceAsString());
            throw $e;
        }

        error_log("=== Fin de la publication du message ===");
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

            $this->hub->publish($update);
        } catch (\Exception $e) {
            error_log("Error publishing reactions: " . $e->getMessage());
            throw $e;
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

            $this->hub->publish($update);
        } catch (\Exception $e) {
            error_log("Error publishing read status: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Publie un événement de frappe dans le hub Mercure
     */
    public function publishTypingEvent(Chatbox $chatbox, int $userId, bool $isTyping): void
    {
        $topic = "/chat/{$chatbox->getId()}";
        
        try {
            $data = [
                'type' => 'typing',
                'userId' => $userId,
                'isTyping' => $isTyping
            ];

            $update = new Update(
                $topic,
                json_encode($data),
                true
            );

            $this->hub->publish($update);
        } catch (\Exception $e) {
            error_log("Error publishing typing event: " . $e->getMessage());
            throw $e;
        }
    }
}
