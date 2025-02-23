<?php

namespace App\Entity;

use App\Repository\MailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un email
 * 
 * Cette classe gère les emails envoyés dans le système,
 * notamment pour les notifications et les communications entre utilisateurs.
 */
#[ORM\Entity(repositoryClass: MailRepository::class)]
class Mail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Destinataire du mail
     */
    #[ORM\Column(length: 255)]
    private ?string $destinataire = null;

    /**
     * Contenu du mail
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    /**
     * Date d'envoi du mail
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    /**
     * Utilisateur ayant envoyé le mail
     */
    #[ORM\ManyToOne(inversedBy: 'mails')]
    private ?Utilisateur $utilisateur = null;

    /**
     * Récupère l'identifiant du mail
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le destinataire du mail
     */
    public function getDestinataire(): ?string
    {
        return $this->destinataire;
    }

    /**
     * Définit le destinataire du mail
     * 
     * @param string $destinataire Le nouveau destinataire du mail
     */
    public function setDestinataire(string $destinataire): static
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    /**
     * Récupère le contenu du mail
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Définit le contenu du mail
     * 
     * @param string $message Le nouveau contenu du mail
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Récupère la date d'envoi du mail
     */
    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    /**
     * Définit la date d'envoi du mail
     * 
     * @param \DateTimeInterface $timestamp La nouvelle date d'envoi
     */
    public function setTimestamp(\DateTimeInterface $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Récupère l'utilisateur ayant envoyé le mail
     */
    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    /**
     * Définit l'utilisateur ayant envoyé le mail
     * 
     * @param Utilisateur|null $utilisateur L'utilisateur expéditeur
     */
    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Simule l'envoi d'un email.
     */
    public function envoyer(): void
    {
        // Simulation de l'envoi d'un email
        echo "Envoi de l'email à {$this->getDestinataire()}...\n";
        echo "Message : {$this->getMessage()}\n";
        echo "Envoyé à : {$this->getTimestamp()->format('Y-m-d H:i:s')}\n";
    }
}
