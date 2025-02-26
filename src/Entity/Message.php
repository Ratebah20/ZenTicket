<?php

namespace App\Entity;

use App\Enum\MessageType;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un message dans une chatbox
 * 
 * Cette classe gère les messages échangés entre les utilisateurs
 * et les techniciens dans le cadre d'un ticket.
 */
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Contenu du message
     */
    #[ORM\Column(length: 255)]
    private ?string $message = null;

    /**
     * Date d'envoi du message
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    /**
     * Type de message (texte, IA, système)
     */
    #[ORM\Column(type: 'string', length: 20, enumType: MessageType::class)]
    private ?MessageType $messageType = MessageType::USER;

    /**
     * Réactions/émojis sur le message
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $reactions = [];

    /**
     * Statut de lecture du message
     */
    #[ORM\Column]
    private bool $isRead = false;

    /**
     * Chatbox dans laquelle le message est envoyé
     */
    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Chatbox $chatbox = null;

    /**
     * Identifiant de l'expéditeur
     */
    #[ORM\Column]
    private ?int $senderID = null;

    /**
     * Récupère l'identifiant du message
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le contenu du message
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Définit le contenu du message
     * 
     * @param string $message Le nouveau contenu du message
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Récupère la date d'envoi du message
     */
    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * Définit la date d'envoi du message
     * 
     * @param \DateTimeInterface $timestamp La nouvelle date d'envoi
     */
    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Récupère le type de message
     */
    public function getMessageType(): ?MessageType
    {
        return $this->messageType;
    }

    /**
     * Définit le type de message
     */
    public function setMessageType(MessageType $messageType): static
    {
        $this->messageType = $messageType;
        return $this;
    }

    /**
     * Récupère les réactions du message
     */
    public function getReactions(): array
    {
        return $this->reactions;
    }

    /**
     * Définit les réactions du message
     */
    public function setReactions(array $reactions): static
    {
        $this->reactions = $reactions;
        return $this;
    }

    /**
     * Ajoute une réaction au message
     */
    public function addReaction(string $emoji, int $userId): static
    {
        if (!isset($this->reactions[$emoji])) {
            $this->reactions[$emoji] = [];
        }
        if (!in_array($userId, $this->reactions[$emoji])) {
            $this->reactions[$emoji][] = $userId;
        }
        return $this;
    }

    /**
     * Retire une réaction du message
     */
    public function removeReaction(string $emoji, int $userId): static
    {
        if (isset($this->reactions[$emoji])) {
            $this->reactions[$emoji] = array_diff($this->reactions[$emoji], [$userId]);
            if (empty($this->reactions[$emoji])) {
                unset($this->reactions[$emoji]);
            }
        }
        return $this;
    }

    /**
     * Vérifie si le message est lu
     */
    public function isRead(): bool
    {
        return $this->isRead;
    }

    /**
     * Définit le statut de lecture du message
     */
    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;
        return $this;
    }

    /**
     * Récupère la chatbox du message
     */
    public function getChatbox(): ?Chatbox
    {
        return $this->chatbox;
    }

    /**
     * Définit la chatbox du message
     * 
     * @param Chatbox|null $chatbox La chatbox à associer
     */
    public function setChatbox(?Chatbox $chatbox): static
    {
        $this->chatbox = $chatbox;

        return $this;
    }

    /**
     * Récupère l'identifiant de l'expéditeur
     */
    public function getSenderID(): ?int
    {
        return $this->senderID;
    }

    /**
     * Définit l'identifiant de l'expéditeur
     * 
     * @param int $senderID Le nouvel identifiant de l'expéditeur
     */
    public function setSenderID(int $senderID): static
    {
        $this->senderID = $senderID;
        return $this;
    }
}
