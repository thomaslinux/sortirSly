<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSortieByCampus($id = 1)
    {
        $qb = $this->createQueryBuilder('s');
        $qb
            ->join('s.campus', 'c')
            ->addSelect('c')
            ->andWhere('s.campus = :campusId')
            ->andWhere('s.etat')
            ->setParameter('campusId', $id);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findSortieDemaree(\DateTime $dateTime,Etat $etat)
    {

        $qb = $this->createQueryBuilder('s');
        $qb

            ->andWhere('s.etat = :etat')
            ->andWhere('s.dateHeureDebut <= :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->setParameter('etat', $etat);


        $query=$qb->getQuery();
        return $query->getResult();
    }










}
