<?php

// src/Repository/TechnicienRepository.php

namespace App\Repository;

use App\Entity\Technicien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Technicien>
 *
 * @method Technicien|null find($id, $lockMode = null, $lockVersion = null)
 * @method Technicien|null findOneBy(array $criteria, array $orderBy = null)
 * @method Technicien[]    findAll()
 * @method Technicien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TechnicienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technicien::class);
    }

    /**
     * Sauvegarde ou met à jour une entité Technicien.
     */
    public function save(Technicien $technicien, bool $flush = false): void
    {
        $this->getEntityManager()->persist($technicien);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une entité Technicien.
     */
    public function remove(Technicien $technicien, bool $flush = false): void
    {
        $this->getEntityManager()->remove($technicien);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve des techniciens qui gèrent actuellement des tickets.
     */
    public function findActiveTechniciens(): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.tickets', 'tk')
            ->andWhere('tk.statut = :status')
            ->setParameter('status', 'en cours')
            ->groupBy('t.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des techniciens sans aucun ticket assigné.
     */
    public function findTechniciensWithoutTickets(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.tickets', 'tk')
            ->andWhere('tk.id IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les tickets gérés par un technicien spécifique.
     */
    public function findTicketsByTechnicien(int $technicienId): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.tickets', 'tk')
            ->andWhere('t.id = :technicienId')
            ->setParameter('technicienId', $technicienId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve le technicien avec le plus de tickets assignés.
     */
    public function findTechnicienWithMostTickets(): ?Technicien
    {
        $result = $this->createQueryBuilder('t')
            ->select('t, COUNT(tk.id) AS ticket_count')
            ->leftJoin('t.tickets', 'tk')
            ->groupBy('t.id')
            ->orderBy('ticket_count', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof Technicien ? $result : $result[0] ?? null;
    }
}
