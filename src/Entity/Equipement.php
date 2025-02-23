<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un équipement réseau
 * 
 * Cette classe permet de gérer les équipements réseau qui peuvent être
 * surveillés via SNMP dans le système.
 */
#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de l'équipement
     */
    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'equipements', cascade: ['persist'])]
    private ?SNMP $sNMP = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom de l'équipement
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom de l'équipement
     * 
     * @param string $nom Le nouveau nom de l'équipement
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSNMP(): ?SNMP
    {
        return $this->sNMP;
    }

    public function setSNMP(?SNMP $sNMP): static
    {
        $this->sNMP = $sNMP;

        return $this;
    }
}
