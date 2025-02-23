<?php

// src/Repository/AdministrateurRepository.php

namespace App\Repository;

use App\Entity\Administrateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Administrateur>
 *
 * @method Administrateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Administrateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Administrateur[]    findAll()
 * @method Administrateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdministrateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Administrateur::class);
    }

    /**
     * Enregistrer ou mettre à jour une entité Administrateur.
     */
    public function save(Administrateur $administrateur, bool $flush = false): void
    {
        $this->getEntityManager()->persist($administrateur);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer une entité Administrateur.
     */
    public function remove(Administrateur $administrateur, bool $flush = false): void
    {
        $this->getEntityManager()->remove($administrateur);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouver tous les administrateurs gérant une catégorie spécifique.
     */
    public function findByCategory(string $categoryName): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.categories', 'c')
            ->andWhere('c.nom = :categoryName')
            ->setParameter('categoryName', $categoryName)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver tous les administrateurs ayant un rapport.
     */
    public function findWithRapport(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.rapport IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les administrateurs sans catégories assignées.
     */
    public function findWithoutCategories(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.categories', 'c')
            ->andWhere('c.id IS NULL')
            ->getQuery()
            ->getResult();
    }
}
