<?php

namespace App\Entity;

use App\Repository\RapportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un rapport (intervention ou statistique)
 * 
 * Cette classe gère deux types de rapports :
 * 1. Rapports d'intervention : liés à un ticket spécifique
 * 2. Rapports statistiques : analyse périodique de plusieurs tickets
 */
#[ORM\Entity(repositoryClass: RapportRepository::class)]
class Rapport
{
    public const TYPE_INTERVENTION = 'intervention';
    public const TYPE_MAINTENANCE = 'maintenance';
    public const TYPE_STATISTIQUES = 'statistiques';

    public const PERIODE_JOURNALIER = 'journalier';
    public const PERIODE_HEBDOMADAIRE = 'hebdomadaire';
    public const PERIODE_MENSUEL = 'mensuel';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $periode = null;

    #[ORM\Column(length: 50)]
    private ?string $service = null;

    #[ORM\ManyToOne]
    private ?Ticket $ticketPrincipal = null;

    #[ORM\ManyToMany(targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $auteur = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $statistiques = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $tempsPasse = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $recommandations = null;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->dateCreation = new \DateTime();
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

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        if (!in_array($type, [self::TYPE_INTERVENTION, self::TYPE_MAINTENANCE, self::TYPE_STATISTIQUES])) {
            throw new \InvalidArgumentException('Type de rapport invalide');
        }
        $this->type = $type;
        return $this;
    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(?string $periode): static
    {
        if ($periode !== null && !in_array($periode, [self::PERIODE_JOURNALIER, self::PERIODE_HEBDOMADAIRE, self::PERIODE_MENSUEL])) {
            throw new \InvalidArgumentException('Période invalide');
        }
        $this->periode = $periode;
        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(string $service): static
    {
        $this->service = $service;
        return $this;
    }

    public function getTicketPrincipal(): ?Ticket
    {
        return $this->ticketPrincipal;
    }

    public function setTicketPrincipal(?Ticket $ticketPrincipal): static
    {
        $this->ticketPrincipal = $ticketPrincipal;
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
        }
        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        $this->tickets->removeElement($ticket);
        return $this;
    }

    public function getAuteur(): ?Personne
    {
        return $this->auteur;
    }

    public function setAuteur(?Personne $auteur): static
    {
        $this->auteur = $auteur;
        return $this;
    }

    public function getStatistiques(): ?array
    {
        return $this->statistiques;
    }

    public function setStatistiques(?array $statistiques): static
    {
        $this->statistiques = $statistiques;
        return $this;
    }

    public function getTempsPasse(): ?int
    {
        return $this->tempsPasse;
    }

    public function setTempsPasse(?int $tempsPasse): static
    {
        $this->tempsPasse = $tempsPasse;
        return $this;
    }

    public function getRecommandations(): ?string
    {
        return $this->recommandations;
    }

    public function setRecommandations(?string $recommandations): static
    {
        $this->recommandations = $recommandations;
        return $this;
    }

    /**
     * Génère un rapport statistique pour une période donnée
     */
    public function genererRapportStatistique(\DateTime $debut, \DateTime $fin): void
    {
        if ($this->type !== self::TYPE_STATISTIQUES) {
            throw new \LogicException('Cette méthode ne peut être utilisée que pour les rapports statistiques');
        }

        $tickets = $this->tickets->toArray();
        
        // Filtrer les tickets pour la période
        $ticketsPeriode = array_filter($tickets, function (Ticket $ticket) use ($debut, $fin) {
            $dateCreation = $ticket->getDateCreation();
            return $dateCreation >= $debut && $dateCreation <= $fin;
        });

        // Calculer les statistiques
        $stats = [
            'total_tickets' => count($ticketsPeriode),
            'tickets_par_statut' => [],
            'delai_moyen_resolution' => 0,
            'tickets_par_categorie' => [],
        ];

        $delaiTotal = 0;
        foreach ($ticketsPeriode as $ticket) {
            // Compter par statut
            $statut = $ticket->getStatut();
            $stats['tickets_par_statut'][$statut] = ($stats['tickets_par_statut'][$statut] ?? 0) + 1;

            // Compter par catégorie
            $categorie = $ticket->getCategorie()->getNom();
            $stats['tickets_par_categorie'][$categorie] = ($stats['tickets_par_categorie'][$categorie] ?? 0) + 1;

            // Calculer délai de résolution pour les tickets résolus
            if ($ticket->getStatut() === Ticket::STATUT_RESOLU && $ticket->getDateResolution()) {
                $delai = $ticket->getDateCreation()->diff($ticket->getDateResolution())->days;
                $delaiTotal += $delai;
            }
        }

        // Calculer le délai moyen
        $ticketsResolus = count(array_filter($ticketsPeriode, fn($t) => $t->getStatut() === Ticket::STATUT_RESOLU));
        $stats['delai_moyen_resolution'] = $ticketsResolus > 0 ? $delaiTotal / $ticketsResolus : 0;

        $this->statistiques = $stats;
    }
}
