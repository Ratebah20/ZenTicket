<?php

namespace App\Entity;

use App\Repository\AdministrateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: AdministrateurRepository::class)]
class Administrateur extends Personne implements PasswordAuthenticatedUserInterface, UserInterface
{
    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\OneToMany(targetEntity: Categorie::class, mappedBy: 'administrateur')]
    private Collection $categories;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Rapport $rapport = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setAdministrateur($this);
        }

        return $this;
    }
    
    public function removeCategory(Categorie $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getAdministrateur() === $this) {
                $category->setAdministrateur(null);
            }
        }

        return $this;
    }

    public function getRapport(): ?Rapport
    {
        return $this->rapport;
    }

    public function setRapport(?Rapport $rapport): static
    {
        $this->rapport = $rapport;

        return $this;
    }

     /**
     * Crée une nouvelle catégorie et l'ajoute à l'administrateur.
     */
    public function creerCategorie(string $nom): void
    {
        $categorie = new Categorie();
        $categorie->setNom($nom);
        $this->addCategory($categorie);
    }

     /**
     * Modifie une catégorie donnée.
     */
    public function modifierCategorie(Categorie $categorie, string $nouveauNom): void
    {
        if ($this->categories->contains($categorie)) {
            $categorie->setNom($nouveauNom);
        } else {
            throw new \Exception("Cette catégorie n'est pas gérée par cet administrateur.");
        }
    }

    public function gererUtilisateurs(): void
    {
        // Implémentation de la méthode gérerUtilisateurs
    }

    public function getPassword(): ?string
    {
        return parent::getPassword();
    }

    public function setPassword(string $password): static
    {
        parent::setPassword($password);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantir que ROLE_ADMIN est toujours présent
        $roles[] = 'ROLE_ADMIN';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Retourne l'identifiant de l'administrateur
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getUsername(): ?string
    {
        return $this->getNom();
    }

    public function eraseCredentials(): void
    {
        // Ne rien faire ici ou effacer des informations sensibles si nécessaire
    }
}
