<?php

namespace App\Repository;

use App\Entity\Ville;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ville>
 */
class VilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);
    }

    public function findVillesBySearch($villeSearch)
    {
        $villeNom = $villeSearch->getNom();



        $qb = $this->createQueryBuilder('v');
        if ($villeNom) {
            $qb
                ->andWhere($qb->expr()->like('v.nom', ':villeNom'))
                ->setParameter('villeNom', '%' . $villeNom . '%');
        }
        $qb
            ->orderBy('v.nom', 'DESC');

        $query = $qb->getQuery();
        return $query->getResult();
    }

}
