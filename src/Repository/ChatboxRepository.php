<?php

// src/Repository/ChatboxRepository.php

namespace App\Repository;

use App\Entity\Chatbox;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chatbox>
 *
 * @method Chatbox|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chatbox|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chatbox[]    findAll()
 * @method Chatbox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatboxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chatbox::class);
    }

    /**
     * Enregistrer ou mettre à jour une entité Chatbox.
     */
    public function save(Chatbox $chatbox, bool $flush = false): void
    {
        $this->getEntityManager()->persist($chatbox);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprimer une entité Chatbox.
     */
    public function remove(Chatbox $chatbox, bool $flush = false): void
    {
        $this->getEntityManager()->remove($chatbox);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouver les chatbox liées à un ticket spécifique.
     */
    public function findByTicket(int $ticketId): ?Chatbox
    {
        return $this->createQueryBuilder('c')
            ->join('c.ticket', 't')
            ->andWhere('t.id = :ticketId')
            ->setParameter('ticketId', $ticketId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouver les chatbox sans messages.
     */
    public function findWithoutMessages(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.messages', 'm')
            ->andWhere('m.id IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les chatbox liées à une IA spécifique.
     */
    public function findByIa(int $iaId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.ia = :iaId')
            ->setParameter('iaId', $iaId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les chatbox ayant détecté un mot-clé spécifique dans les messages.
     */
    public function findByMessageKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('c')
            ->join('c.messages', 'm')
            ->andWhere('m.message LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }
}
