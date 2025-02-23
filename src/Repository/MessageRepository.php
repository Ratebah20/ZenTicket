<?php

// src/Repository/MessageRepository.php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Sauvegarde ou met à jour une entité Message.
     */
    public function save(Message $message, bool $flush = false): void
    {
        $this->getEntityManager()->persist($message);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une entité Message.
     */
    public function remove(Message $message, bool $flush = false): void
    {
        $this->getEntityManager()->remove($message);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve des messages par Chatbox.
     */
    public function findByChatbox(int $chatboxId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.chatbox = :chatboxId')
            ->setParameter('chatboxId', $chatboxId)
            ->orderBy('m.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des messages envoyés par un expéditeur spécifique.
     */
    public function findBySender(int $senderId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.senderID = :senderId')
            ->setParameter('senderId', $senderId)
            ->orderBy('m.timestamp', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des messages dans une plage de dates spécifique.
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.timestamp BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('m.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des messages non lus (statutMessage = false).
     */
    public function findUnreadMessages(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.statutMessage = :unread')
            ->setParameter('unread', false)
            ->orderBy('m.timestamp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des messages contenant un mot-clé spécifique.
     */
    public function findByMessageKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.message LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('m.timestamp', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

