<?php

namespace App\Repository;

use App\Entity\Etat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etat>
 */
class EtatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etat::class);
    }


    public function findEtat()
    {
        $qb = $this->createQueryBuilder('e');
        $qb
            ->where('e.nom IN (:nom)' )
            ->setParameter('nom', ['Ouverte','Cloturee','En cours','Terminee','Historisee']);

        $query = $qb->getQuery();
        return $query->getResult();
    }

}
