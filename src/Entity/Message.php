<?php

namespace App\Entity;

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
     * Statut du message
     */
    #[ORM\Column]
    private ?bool $statutMessage = null;

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
     * Récupère le statut du message
     */
    public function isStatutMessage(): ?bool
    {
        return $this->statutMessage;
    }

    /**
     * Définit le statut du message
     * 
     * @param bool $statutMessage Le nouveau statut du message
     */
    public function setStatutMessage(bool $statutMessage): static
    {
        $this->statutMessage = $statutMessage;

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
