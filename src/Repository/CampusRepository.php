<?php

namespace App\Repository;

use App\Entity\Campus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campus>
 */
class CampusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }

    public function findCampusBySearch(mixed $campusSearch)
    {
        $campusNom = $campusSearch->getNom();


        $qb = $this->createQueryBuilder('c');
        if ($campusNom) {
            $qb
                ->andWhere($qb->expr()->like('c.nom', ':campusNom'))
                ->setParameter('campusNom', '%' . $campusNom . '%');
        }
        $qb
            ->orderBy('c.nom', 'DESC');

        $query = $qb->getQuery();
        return $query->getResult();
    }


}
