<?php

// src/Repository/RapportRepository.php

namespace App\Repository;

use App\Entity\Rapport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rapport>
 *
 * @method Rapport|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rapport|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rapport[]    findAll()
 * @method Rapport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rapport::class);
    }

    /**
     * Trouve les rapports par type et période
     */
    public function findByTypeAndPeriode(string $type, ?string $periode = null, ?\DateTime $debut = null, ?\DateTime $fin = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.type = :type')
            ->setParameter('type', $type)
            ->orderBy('r.dateCreation', 'DESC');

        if ($periode) {
            $qb->andWhere('r.periode = :periode')
               ->setParameter('periode', $periode);
        }

        if ($debut && $fin) {
            $qb->andWhere('r.dateCreation BETWEEN :debut AND :fin')
               ->setParameter('debut', $debut)
               ->setParameter('fin', $fin);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les rapports d'intervention pour un ticket spécifique
     */
    public function findInterventionsForTicket(int $ticketId)
    {
        return $this->createQueryBuilder('r')
            ->where('r.type = :type')
            ->andWhere('r.ticketPrincipal = :ticketId')
            ->setParameter('type', Rapport::TYPE_INTERVENTION)
            ->setParameter('ticketId', $ticketId)
            ->orderBy('r.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les rapports par service
     */
    public function findByService(string $service, ?\DateTime $debut = null, ?\DateTime $fin = null)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.service = :service')
            ->setParameter('service', $service)
            ->orderBy('r.dateCreation', 'DESC');

        if ($debut && $fin) {
            $qb->andWhere('r.dateCreation BETWEEN :debut AND :fin')
               ->setParameter('debut', $debut)
               ->setParameter('fin', $fin);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les derniers rapports créés
     */
    public function findLatest(int $limit = 10)
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.dateCreation', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
