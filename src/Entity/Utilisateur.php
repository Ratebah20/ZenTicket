<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Entité représentant un utilisateur du système
 * 
 * Cette classe gère les utilisateurs qui peuvent créer des tickets
 * et interagir avec le système de support.
 */
#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur extends Personne implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Collection des tickets créés par cet utilisateur
     * 
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Ticket::class)]
    #[Groups(['utilisateur:item:read'])]
    private Collection $tickets;

    public function __construct()
    {
        parent::__construct();
        $this->tickets = new ArrayCollection();
        $this->addRole('ROLE_USER');
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setUtilisateur($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            if ($ticket->getUtilisateur() === $this) {
                $ticket->setUtilisateur(null);
            }
        }

        return $this;
    }
}
