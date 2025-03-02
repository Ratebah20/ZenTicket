<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "utilisateur" => Utilisateur::class,
    "technicien" => Technicien::class,
    "administrateur" => Administrateur::class
])]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
abstract class Personne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'utilisateur:read', 'technicien:read', 'ticket:read', 'commentaire:read', 'message:read'])]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Groups(['user:read', 'utilisateur:read', 'utilisateur:write', 'technicien:read', 'technicien:write', 'ticket:read', 'commentaire:read', 'message:read'])]
    protected ?string $nom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas un email valide')]
    #[Groups(['user:read', 'utilisateur:read', 'utilisateur:write', 'technicien:read', 'technicien:write', 'ticket:read', 'commentaire:read', 'message:read'])]
    protected ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire', groups: ['create'])]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères')]
    #[Groups(['utilisateur:write', 'technicien:write'])]
    protected ?string $password = null;

    #[ORM\Column(type: "json")]
    #[Groups(['user:read', 'utilisateur:read', 'utilisateur:write', 'technicien:read', 'technicien:write'])]
    protected array $roles = [];

    public function __construct()
    {
        $this->roles = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = strtolower($email);
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function addRole(string $role): static
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function removeRole(string $role): static
    {
        $index = array_search($role, $this->roles);
        if ($index !== false) {
            unset($this->roles[$index]);
            $this->roles = array_values($this->roles);
        }
        return $this;
    }
}
