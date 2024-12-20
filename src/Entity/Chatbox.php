<?php

namespace App\Entity;

use App\Repository\ChatboxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatboxRepository::class)]
class Chatbox
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'chatbox', cascade: ['persist', 'remove'])]
    private ?Ticket $ticket = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'chatbox')]
    private Collection $messages;

    #[ORM\ManyToOne(inversedBy: 'chatBoxes')]
    private ?IA $ia = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        // unset the owning side of the relation if necessary
        if ($ticket === null && $this->ticket !== null) {
            $this->ticket->setChatbox(null);
        }

        // set the owning side of the relation if necessary
        if ($ticket !== null && $ticket->getChatbox() !== $this) {
            $ticket->setChatbox($this);
        }

        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setChatbox($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChatbox() === $this) {
                $message->setChatbox(null);
            }
        }

        return $this;
    }

    public function getIa(): ?IA
    {
        return $this->ia;
    }

    public function setIa(?IA $ia): static
    {
        $this->ia = $ia;

        return $this;
    }
}
