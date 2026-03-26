<?php

namespace App\Repository;

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
            ->leftJoin('s.campus', 'c')
            ->addSelect('c')
            ->leftJoin('s.etat', 'etat')
            ->addSelect('etat')
            ->leftJoin('s.inscriptions', 'inscriptions')
            ->addSelect('inscriptions')
            ->andWhere('s.campus = :campusId')
            ->setParameter('campusId', $id);
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

    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
