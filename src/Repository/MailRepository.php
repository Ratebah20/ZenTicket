<?php

// src/Repository/MailRepository.php

namespace App\Repository;

use App\Entity\Mail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mail>
 *
 * @method Mail|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mail|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mail[]    findAll()
 * @method Mail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mail::class);
    }

    /**
     * Sauvegarde ou met à jour une entité Mail.
     */
    public function save(Mail $mail, bool $flush = false): void
    {
        $this->getEntityManager()->persist($mail);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une entité Mail.
     */
    public function remove(Mail $mail, bool $flush = false): void
    {
        $this->getEntityManager()->remove($mail);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les emails envoyés à un destinataire spécifique.
     */
    public function findByDestinataire(string $destinataire): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.destinataire = :destinataire')
            ->setParameter('destinataire', $destinataire)
            ->orderBy('m.timestamp', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des emails envoyés dans une plage de dates spécifique.
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.timestamp BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('m.timestamp', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les emails associés à un utilisateur spécifique.
     */
    public function findByUtilisateur(int $utilisateurId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.utilisateur = :utilisateurId')
            ->setParameter('utilisateurId', $utilisateurId)
            ->orderBy('m.timestamp', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve des emails contenant un mot-clé spécifique dans le message.
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
