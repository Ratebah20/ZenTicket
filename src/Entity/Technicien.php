<?php

namespace App\Entity;

use App\Repository\TechnicienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
class Technicien extends Personne implements PasswordAuthenticatedUserInterface, UserInterface
{
    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'technicien')]
    #[Groups(['technicien:read'])]
    private Collection $tickets;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['technicien:read'])]
    #[Assert\Length(max: 255)]
    private ?string $specialite = null;

    public function __construct()
    {
        parent::__construct();
        $this->tickets = new ArrayCollection();
        $this->addRole('ROLE_TECHNICIEN');
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): static
    {
        $this->specialite = $specialite;
        return $this;
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
            $ticket->setTechnicien($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            if ($ticket->getTechnicien() === $this) {
                $ticket->setTechnicien(null);
            }
        }

        return $this;
    }

    /**
     * Ajoute une solution à un ticket
     * @throws \Exception si le technicien n'est pas assigné au ticket
     */
    public function ajouterSolution(Ticket $ticket, string $solution): void
    {
        if ($ticket->getTechnicien() !== $this) {
            throw new \Exception("Vous n'êtes pas assigné à ce ticket.");
        }
        
        $ticket->setSolution($solution);
    }

    /**
     * Prend en charge un ticket
     * @throws \Exception si le ticket est déjà pris en charge
     */
    public function prendreEnCharge(Ticket $ticket): void
    {
        if ($ticket->getTechnicien() !== null) {
            throw new \Exception("Ce ticket est déjà pris en charge.");
        }
        
        $ticket->setStatut(Ticket::STATUT_EN_COURS);
        $ticket->setTechnicien($this);
    }

    /**
     * Modifie le statut d'un ticket
     * @throws \Exception si le technicien n'est pas assigné au ticket
     */
    public function modifierStatut(Ticket $ticket, string $statut): void
    {
        if ($ticket->getTechnicien() !== $this) {
            throw new \Exception("Vous n'êtes pas assigné à ce ticket.");
        }

        if (!in_array($statut, [
            Ticket::STATUT_EN_COURS,
            Ticket::STATUT_RESOLU
        ])) {
            throw new \Exception("Statut invalide.");
        }

        $ticket->setStatut($statut);
        
        if ($statut === Ticket::STATUT_RESOLU) {
            $ticket->setDateResolution(new \DateTime());
        }
    }

    /**
     * Clôture un ticket
     * @throws \Exception si les conditions ne sont pas remplies
     */
    public function cloturerTicket(Ticket $ticket): void
    {
        if ($ticket->getTechnicien() !== $this) {
            throw new \Exception("Vous n'êtes pas assigné à ce ticket.");
        }

        if ($ticket->getStatut() !== Ticket::STATUT_RESOLU) {
            throw new \Exception("Le ticket doit être 'résolu' pour être clôturé.");
        }

        if (!$ticket->isSolutionValidee()) {
            throw new \Exception("La solution doit être validée avant de clôturer le ticket.");
        }

        $ticket->setStatut(Ticket::STATUT_CLOTURE);
        $ticket->setDateCloture(new \DateTime());
    }

    /**
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
        // Ne rien faire ici
    }
}
