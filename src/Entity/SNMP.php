<?php

namespace App\Entity;

use App\Repository\SNMPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une surveillance SNMP
 * 
 * Cette classe gère les surveillances SNMP des équipements réseau,
 * permettant de collecter des informations sur leur état et leurs performances.
 */
#[ORM\Entity(repositoryClass: SNMPRepository::class)]
class SNMP
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la surveillance SNMP
     */
    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'sNMP', targetEntity: Equipement::class, cascade: ['persist'])]
    private Collection $equipements;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant de la surveillance
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom de la surveillance SNMP
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom de la surveillance SNMP
     * 
     * @param string $nom Le nouveau nom
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Equipement>
     */
    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    /**
     * Ajoute un équipement à la surveillance
     * 
     * @param Equipement $equipement L'équipement à ajouter
     */
    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements->add($equipement);
            $equipement->setSNMP($this);
        }

        return $this;
    }

    /**
     * Retire un équipement de la surveillance
     * 
     * @param Equipement $equipement L'équipement à retirer
     */
    public function removeEquipement(Equipement $equipement): static
    {
        if ($this->equipements->removeElement($equipement)) {
            if ($equipement->getSNMP() === $this) {
                $equipement->setSNMP(null);
            }
        }

        return $this;
    }

    /**
     * Simule la surveillance des équipements.
     */
    public function surveillerEquipements(): void
    {
        foreach ($this->equipements as $equipement) {
            // Attribue aléatoirement un état
            $etat = random_int(0, 1) === 0 ? 'fonctionne' : 'problème détecté';
            echo "Équipement : " . $equipement->getNom() . " - État : " . $etat . "\n";
        }
    }
}
