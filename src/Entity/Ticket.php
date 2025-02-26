<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    public const STATUT_NOUVEAU = 'nouveau';
    public const STATUT_EN_COURS = 'en cours';
    public const STATUT_RESOLU = 'résolu';
    public const STATUT_CLOTURE = 'clôturé';

    public const PRIORITE_BASSE = 'basse';
    public const PRIORITE_NORMALE = 'normale';
    public const PRIORITE_HAUTE = 'haute';
    public const PRIORITE_URGENTE = 'urgente';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ticket:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['ticket:read', 'ticket:write'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['ticket:read', 'ticket:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: [
        self::STATUT_NOUVEAU,
        self::STATUT_EN_COURS,
        self::STATUT_RESOLU,
        self::STATUT_CLOTURE
    ])]
    #[Groups(['ticket:read', 'ticket:write'])]
    private ?string $statut = self::STATUT_NOUVEAU;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: [
        self::PRIORITE_BASSE,
        self::PRIORITE_NORMALE,
        self::PRIORITE_HAUTE,
        self::PRIORITE_URGENTE
    ])]
    #[Groups(['ticket:read', 'ticket:write'])]
    private ?string $priorite = self::PRIORITE_NORMALE;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['ticket:read'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['ticket:read'])]
    private ?\DateTimeInterface $dateResolution = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['ticket:read'])]
    private ?\DateTimeInterface $dateCloture = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'utilisateur est obligatoire")]
    #[Groups(['ticket:read', 'ticket:write'])]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['ticket:read', 'ticket:write'])]
    private ?Technicien $technicien = null;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Commentaire::class, orphanRemoval: true)]
    #[Groups(['ticket:read'])]
    private Collection $commentaires;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['ticket:read', 'ticket:write'])]
    private ?string $solution = null;

    #[ORM\Column]
    #[Groups(['ticket:read'])]
    private ?bool $solutionValidee = false;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "La catégorie est obligatoire")]
    #[Groups(['ticket:read', 'ticket:write'])]
    private ?Categorie $categorie = null;

    #[ORM\OneToOne(inversedBy: 'ticket', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Chatbox $chatbox = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->commentaires = new ArrayCollection();
        $this->statut = self::STATUT_NOUVEAU;
        $this->priorite = self::PRIORITE_NORMALE;
        $this->solutionValidee = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): static
    {
        $this->priorite = $priorite;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateResolution(): ?\DateTimeInterface
    {
        return $this->dateResolution;
    }

    public function setDateResolution(?\DateTimeInterface $dateResolution): static
    {
        $this->dateResolution = $dateResolution;
        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->dateCloture;
    }

    public function setDateCloture(?\DateTimeInterface $dateCloture): static
    {
        $this->dateCloture = $dateCloture;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getTechnicien(): ?Technicien
    {
        return $this->technicien;
    }

    public function setTechnicien(?Technicien $technicien): static
    {
        $this->technicien = $technicien;
        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setTicket($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getTicket() === $this) {
                $commentaire->setTicket(null);
            }
        }

        return $this;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(?string $solution): static
    {
        $this->solution = $solution;
        return $this;
    }

    public function isSolutionValidee(): ?bool
    {
        return $this->solutionValidee;
    }

    public function setSolutionValidee(bool $solutionValidee): static
    {
        $this->solutionValidee = $solutionValidee;
        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;
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

    public function peutEtreModifie(): bool
    {
        return !in_array($this->statut, [self::STATUT_CLOTURE]);
    }

    public function peutEtreResolu(): bool
    {
        return $this->technicien !== null && $this->statut === self::STATUT_EN_COURS;
    }

    public function peutEtreValide(): bool
    {
        return $this->statut === self::STATUT_RESOLU && !$this->solutionValidee;
    }

    public function getPrioriteClass(): string
    {
        return match ($this->priorite) {
            self::PRIORITE_BASSE => 'bg-success',
            self::PRIORITE_NORMALE => 'bg-info',
            self::PRIORITE_HAUTE => 'bg-warning',
            self::PRIORITE_URGENTE => 'bg-danger',
            default => 'bg-secondary'
        };
    }
}
