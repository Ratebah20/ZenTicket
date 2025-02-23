<?php

// src/Repository/TicketRepository.php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    /**
     * Sauvegarde ou met à jour une entité Ticket.
     */
    public function save(Ticket $ticket, bool $flush = false): void
    {
        $this->getEntityManager()->persist($ticket);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une entité Ticket.
     */
    public function remove(Ticket $ticket, bool $flush = false): void
    {
        $this->getEntityManager()->remove($ticket);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve des tickets par statut.
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.statut = :status')
            ->setParameter('status', $status)
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des tickets par ID utilisateur.
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.utilisateur = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des tickets assignés à un technicien spécifique.
     */
    public function findByTechnician(int $technicianId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.technicien = :technicianId')
            ->setParameter('technicianId', $technicianId)
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des tickets créés dans une plage de dates spécifique.
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.dateCreation BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('t.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des tickets non résolus (statut différent de 'clôturé').
     */
    public function findUnresolvedTickets(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.statut != :closedStatus')
            ->setParameter('closedStatus', 'clôturé')
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des tickets contenant un mot-clé spécifique dans leur description.
     */
    public function findByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.description LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des nouveaux tickets (non assignés et statut 'nouveau').
     */
    public function findNewTickets(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.technicien IS NULL')
            ->andWhere('t.statut = :statut')
            ->setParameter('statut', 'nouveau')
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
