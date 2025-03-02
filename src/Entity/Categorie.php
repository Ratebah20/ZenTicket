<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant une catégorie de tickets
 * 
 * Cette classe permet de catégoriser les tickets pour une meilleure
 * organisation et gestion des problèmes.
 */
#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['categorie:read', 'ticket:read'])]
    private ?int $id = null;

    /**
     * Nom de la catégorie
     */
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(max: 50, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères')]
    #[Groups(['categorie:read', 'categorie:write', 'ticket:read'])]
    private ?string $nom = null;

    /**
     * Description détaillée de la catégorie
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La description est obligatoire')]
    #[Assert\Length(max: 255, maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères')]
    #[Groups(['categorie:read', 'categorie:write'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[Groups(['categorie:read'])]
    private ?Administrateur $administrateur = null;

    /**
     * Collection des tickets dans cette catégorie
     * 
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Ticket::class)]
    #[Groups(['categorie:item:read'])]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom de la catégorie
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom de la catégorie
     * 
     * @param string $nom Le nouveau nom de la catégorie
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * Récupère la description de la catégorie
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la catégorie
     * 
     * @param string $description La nouvelle description
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getAdministrateur(): ?Administrateur
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?Administrateur $administrateur): static
    {
        $this->administrateur = $administrateur;
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
            $ticket->setCategorie($this);
        }
        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getCategorie() === $this) {
                $ticket->setCategorie(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? '';
    }
}
