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

    public function findSortieByCampus(
        $campusId = 1
    )
    {
        // TODO récupérer avec l'etat, le nom de la sortie et les dates
        $qb = $this->createQueryBuilder('s');
        $qb
            ->leftJoin('s.campus', 'c')
            ->addSelect('c')
            ->leftJoin('s.etat', 'e')
            ->addSelect('e')
            ->leftJoin('s.inscriptions', 'i')
            ->addSelect('i');
        $qb
            ->andWhere('s.campus = :campusId')
            ->setParameter('campusId', $campusId);

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findAllFiltred(): array
    {
        $dql = "
            SELECT s, campus, etat FROM App\Entity\Sortie AS s
            LEFT JOIN s.campus AS campus
            LEFT JOIN s.etat AS etat
            ORDER BY s.dateHeureDebut DESC
        ";

        $query = $this->getEntityManager()->createQuery($dql);
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
    public function findSortieACloturer(\DateTime $dateTime,Etat $etat)
    {

        $qb = $this->createQueryBuilder('s');
        $qb

            ->andWhere('s.etat = :etat')
            ->andWhere('s.dateLimiteInscription <= :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->setParameter('etat', $etat);


        $query=$qb->getQuery();
        return $query->getResult();
    }










}
