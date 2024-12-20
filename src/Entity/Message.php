<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column]
    private ?bool $statutMessage = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Chatbox $chatbox = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function isStatutMessage(): ?bool
    {
        return $this->statutMessage;
    }

    public function setStatutMessage(bool $statutMessage): static
    {
        $this->statutMessage = $statutMessage;

        return $this;
    }

    public function getChatbox(): ?Chatbox
    {
        return $this->chatbox;
    }

    public function setChatbox(?Chatbox $chatbox): static
    {
        $this->chatbox = $chatbox;

        return $this;
    }
}
