<?php

// src/Repository/CategorieRepository.php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    /**
     * Sauvegarde ou met à jour une entité Categorie.
     */
    public function save(Categorie $categorie, bool $flush = false): void
    {
        $this->getEntityManager()->persist($categorie);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une entité Categorie.
     */
    public function remove(Categorie $categorie, bool $flush = false): void
    {
        $this->getEntityManager()->remove($categorie);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve des catégories gérées par un administrateur spécifique.
     */
    public function findByAdministrateur(int $administrateurId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.administrateur = :administrateurId')
            ->setParameter('administrateurId', $administrateurId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des catégories par leur nom (insensible à la casse).
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('LOWER(c.nom) = :name')
            ->setParameter('name', strtolower($name))
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve toutes les catégories sans administrateur associé.
     */
    public function findWithoutAdministrateur(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.administrateur IS NULL')
            ->getQuery()
            ->getResult();
    }
}
