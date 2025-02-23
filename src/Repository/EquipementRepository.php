<?php

// src/Repository/EquipementRepository.php

namespace App\Repository;

use App\Entity\Equipement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipement>
 *
 * @method Equipement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipement[]    findAll()
 * @method Equipement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipement::class);
    }

    /**
     * Sauvegarde ou met à jour une entité Equipement.
     */
    public function save(Equipement $equipement, bool $flush = false): void
    {
        $this->getEntityManager()->persist($equipement);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une entité Equipement.
     */
    public function remove(Equipement $equipement, bool $flush = false): void
    {
        $this->getEntityManager()->remove($equipement);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les équipements associés à un SNMP spécifique.
     */
    public function findBySNMP(int $snmpId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.sNMP = :snmpId')
            ->setParameter('snmpId', $snmpId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des équipements par nom (insensible à la casse).
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('LOWER(e.nom) = :name')
            ->setParameter('name', strtolower($name))
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des équipements sans SNMP associé.
     */
    public function findWithoutSNMP(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.sNMP IS NULL')
            ->getQuery()
            ->getResult();
    }
}

