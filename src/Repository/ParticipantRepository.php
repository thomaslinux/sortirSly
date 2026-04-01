<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Participant>
 */
class ParticipantRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Participant) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

//    Login : username ou email + actif seulement
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->createQueryBuilder('p')
            ->where('p.username = :query OR p.email = :query')
            ->andWhere('p.actif = true')
            ->setParameter('query', $identifier)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            throw new \Symfony\Component\Security\Core\Exception\UserNotFoundException();
        }
        return $user;
    }

    public function findUserBySearch(mixed $userSearch)
    {
        $userNom = $userSearch->getNom();

        $qb = $this->createQueryBuilder('u');
        if ($userNom) {
            $qb
                ->andWhere($qb->expr()->like('u.username', ':userNom'))
                ->setParameter('userNom', '%' . $userNom . '%');
        }
        $qb
            ->orderBy('u.nom', 'DESC');

        $query = $qb->getQuery();
        return $query->getResult();
    }
}
