<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Sortie>
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSortiesBySearch($sortieSearch)
    {
        $sortieNom = $sortieSearch->getNom();
        $campus = $sortieSearch->getCampus();
        $dateHeureDebut = $sortieSearch->getDateHeureDebut();
        $dateHeureFin = $sortieSearch->getDateHeureFin();
        $qb = $this->createQueryBuilder('s');
        $qb
            ->leftJoin('s.campus', 'c')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.inscriptions', 'i')
            ->addSelect('c')
            ->addSelect('e')
            ->addSelect('i');
        $qb
            ->andWhere('e.nom != :historisee')
            ->setParameter('historisee', 'Historisee');
        if ($sortieNom) {
            $qb
                ->andWhere($qb->expr()->like('s.nom', ':sortieNom'))
                ->setParameter('sortieNom', '%' . $sortieNom . '%');
        }
        if ($campus) {
            $qb
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $campus);
        }
        if ($dateHeureDebut) {
            $qb
                ->andWhere('s.dateHeureDebut >= :dateHeureDebut')
                ->setParameter('dateHeureDebut', $dateHeureDebut);
        }
        if ($dateHeureFin) {
            $qb
                ->andWhere('s.dateHeureDebut <= :dateHeureFin')
                ->setParameter('dateHeureFin', $dateHeureFin);
        }
        $qb
            ->orderBy('s.dateHeureDebut', 'DESC');

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

   public function findSortieEtatAModifier(\DateTime $dateTime, \DateTime $dateTime2, array $etat)
    {

        $qb = $this->createQueryBuilder('s');
        $qb
            ->andWhere('s.etat IN (:etats)')
            ->andWhere(
                $qb->expr()->orX(
                    's.dateHeureDebut <= :dateTime',
                    's.dateLimiteInscription <= :dateTime2'
                )
            )
            ->setParameter('etats', $etat)
            ->setParameter('dateTime', $dateTime)
            ->setParameter('dateTime2', $dateTime2);


        $query = $qb->getQuery();
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
