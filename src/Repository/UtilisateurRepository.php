<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Récupère tous les tickets liés à un utilisateur donné.
     */
    public function findTicketsByUtilisateur(int $utilisateurId): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.tickets', 't')
            ->addSelect('t')
            ->where('u.id = :id')
            ->setParameter('id', $utilisateurId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche un utilisateur par email.
     */
    public function findByEmail(string $email): ?Utilisateur
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Sauvegarde ou met à jour une entité Utilisateur.
     *
     * @param Utilisateur $utilisateur
     * @param bool $flush
     */
    public function save(Utilisateur $utilisateur, bool $flush = false): void
    {
        $this->getEntityManager()->persist($utilisateur);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une entité Utilisateur.
     *
     * @param Utilisateur $utilisateur
     * @param bool $flush
     */
    public function remove(Utilisateur $utilisateur, bool $flush = false): void
    {
        $this->getEntityManager()->remove($utilisateur);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Recherche des utilisateurs par statut.
     *
     * @param string $status
     * @return array
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.tickets', 't')
            ->andWhere('t.statut = :status')
            ->setParameter('status', $status)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des utilisateurs avec des tickets non résolus.
     *
     * @return array
     */
    public function findWithUnresolvedTickets(): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.tickets', 't')
            ->andWhere('t.statut != :closedStatus')
            ->setParameter('closedStatus', 'clôturé')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des utilisateurs par mot-clé dans les descriptions de tickets.
     *
     * @param string $keyword
     * @return array
     */
    public function findByTicketKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.tickets', 't')
            ->andWhere('t.description LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
