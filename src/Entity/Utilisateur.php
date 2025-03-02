<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
     * Prénom de l'utilisateur
     */
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Groups(['utilisateur:read', 'utilisateur:write', 'ticket:read', 'commentaire:read', 'message:read'])]
    private ?string $prenom = null;

    /**
     * Numéro de téléphone de l'utilisateur
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    private ?string $telephone = null;

    /**
     * Service ou département de l'utilisateur
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['utilisateur:read', 'utilisateur:write', 'ticket:read'])]
    private ?string $service = null;

    /**
     * Collection des tickets créés par cet utilisateur
     * 
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Ticket::class)]
    #[Groups(['utilisateur:item:read'])]
    private Collection $tickets;

    /**
     * Date de dernière connexion
     */
    #[ORM\Column(nullable: true)]
    #[Groups(['utilisateur:read'])]
    private ?\DateTimeImmutable $lastLogin = null;

    /**
     * Indique si le compte est actif
     */
    #[ORM\Column]
    #[Groups(['utilisateur:read', 'utilisateur:write'])]
    private ?bool $isActive = true;

    public function __construct()
    {
        parent::__construct();
        $this->tickets = new ArrayCollection();
        $this->addRole('ROLE_USER');
        $this->isActive = true;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): static
    {
        $this->service = $service;
        return $this;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeImmutable $lastLogin): static
    {
        $this->lastLogin = $lastLogin;
        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }
}
