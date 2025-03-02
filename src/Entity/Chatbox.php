<?php

namespace App\Entity;

use App\Repository\ChatboxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une boîte de discussion
 * 
 * Cette classe gère les conversations entre les utilisateurs et les techniciens
 * concernant un ticket spécifique.
 */
#[ORM\Entity(repositoryClass: ChatboxRepository::class)]
class Chatbox
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Ticket associé à cette chatbox
     */
    #[ORM\OneToOne(mappedBy: 'chatbox', cascade: ['persist', 'remove'])]
    private ?Ticket $ticket = null;

    /**
     * Collection des messages de la conversation
     * 
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'chatbox')]
    private Collection $messages;

    #[ORM\ManyToOne(inversedBy: 'chatBoxes')]
    private ?IA $ia = null;

    /**
     * Date de création de la chatbox
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * Indique si c'est une chatbox temporaire pour l'assistant IA
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isTemporary = false;

    /**
     * Initialise une nouvelle chatbox avec une collection de messages vide
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant de la chatbox
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le ticket associé à la chatbox
     */
    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    /**
     * Définit le ticket associé à la chatbox
     * 
     * @param Ticket|null $ticket Le ticket à associer
     */
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
     * Récupère tous les messages de la chatbox
     * 
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * Ajoute un message à la chatbox
     * 
     * @param Message $message Le message à ajouter
     */
    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setChatbox($this);
        }

        return $this;
    }

    /**
     * Retire un message de la chatbox
     * 
     * @param Message $message Le message à retirer
     */
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

    /**
     * Récupère l'IA associée à la chatbox
     */
    public function getIa(): ?IA
    {
        return $this->ia;
    }

    /**
     * Définit l'IA associée à la chatbox
     * 
     * @param IA|null $ia L'IA à associer
     */
    public function setIa(?IA $ia): static
    {
        $this->ia = $ia;

        return $this;
    }

    /**
     * Récupère la date de création de la chatbox
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Définit la date de création de la chatbox
     * 
     * @param \DateTimeInterface|null $createdAt La date de création
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }

    /**
     * Vérifie si la chatbox est temporaire (pour l'assistant IA)
     */
    public function isTemporary(): bool
    {
        return $this->isTemporary;
    }

    /**
     * Définit si la chatbox est temporaire (pour l'assistant IA)
     * 
     * @param bool $isTemporary Vrai si temporaire, faux sinon
     */
    public function setIsTemporary(bool $isTemporary): static
    {
        $this->isTemporary = $isTemporary;
        
        return $this;
    }

    public function envoyerMessage(int $sender_id, string $message): void
    {
        $newMessage = new Message();
        $newMessage->setSenderID($sender_id) 
                   ->setMessage($message)
                   ->setTimestamp(new \DateTime());
        $this->addMessage($newMessage);

        echo "Message envoyé par l'utilisateur #$sender_id dans la chatbox #{$this->getId()}";
    }

    public function recevoirMessages(): Collection
    {
        return $this->getMessages();
    }

    public function interagirAvecIA(string $message): string
    {
        if ($this->getIa() === null) {
            throw new \Exception("Aucune IA n'est liée à cette chatbox.");
        }

        return $this->getIa()->reponse($message);
    }

    public function proposerCreationTicket(): void
    {
        $messages = $this->getMessages();
        foreach ($messages as $message) {
            if (stripos($message->getMessage(), 'problème') !== false) {
                echo "Un problème détecté dans la chatbox #{$this->getId()}, proposition de création de ticket...";
                break;
            }
        }
    }
}