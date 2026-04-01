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

    public function findSortiesBySearch($sortieSearch, $user)
    {
        $sortieNom = $sortieSearch->getNom();
        $campus = $sortieSearch->getCampus();
        $dateHeureDebut = $sortieSearch->getDateHeureDebut();
        $dateHeureFin = $sortieSearch->getDateHeureFin();
        $organisateur = $sortieSearch->getOrganisateur();
        $inscrit = $sortieSearch->getInscrit();
        $pasInscrit = $sortieSearch->getPasInscrit();
        $sortiesPassees = $sortieSearch->getSortiesPassees();
        $qb = $this->createQueryBuilder('s');
        $qb
            ->leftJoin('s.campus', 'c')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.inscriptions', 'i')
            ->leftJoin('s.organisateur', 'o')
            ->addSelect('c')
            ->addSelect('e')
            ->addSelect('i')
            ->addSelect('o');
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
        if ($organisateur) {
            $qb
                ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $user);
        }
        if ($inscrit) {
            $qb
                ->andWhere(':user MEMBER OF s.inscriptions')
                ->setParameter('user', $user);
        }
        if ($pasInscrit) {
            $qb
                ->andWhere(':user NOT MEMBER OF s.inscriptions')
                ->setParameter('user', $user);
        }
        if ($sortiesPassees) {
            $date = new \DateTime();
            date_format($date, 'd-m-Y');

            $qb->andWhere('s.dateHeureDebut < :today')
                ->setParameter('today', $date);
        }
        $qb
            ->orderBy('s.dateHeureDebut', 'DESC');

        $query = $qb->getQuery();
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

}
