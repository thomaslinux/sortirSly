<?php

namespace App\Service;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SortieService
{
    /**
     * @param SortieRepository $sortieRepository
     * @param EtatRepository $etatRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SortieRepository $sortieRepository,
        private EtatRepository $etatRepository,
    ) {}
public function MaJEtat(){

    $now = new \DateTime('now');
    $sortieRepository = $this->entityManager->getRepository(Sortie::class);
    $etatRepository = $this->entityManager->getRepository(Etat::class);
    $etats = $etatRepository->findEtat();
    $etatSortieAModifier = $sortieRepository->findSortieEtatAModifier($now, $now, $etats);

    foreach ($etatSortieAModifier as $sortie) {

        $dateLimite = (clone $sortie->getDateHeureDebut())->modify('+' . $sortie->getDuree() . ' minutes');
        $dateLimiteHisto = (clone $sortie->getDateHeureDebut())->modify('+1 month');
        $dateLimiteIns = (clone $sortie->getDateLimiteInscription());

        if ($dateLimiteIns <= $now && $sortie->getEtat()->getnom() == 'Ouverte') {
            $sortie->setEtat($etats[1]);
        }

        if ($dateLimite <= $now) {
            if (
                $sortie->getEtat()->getnom() == 'Ouverte' || $sortie->getEtat()->getnom() == 'Cloturee'
            ) {
                $sortie->setEtat($etats[2]);
            }
            if ($sortie->getEtat()->getnom() == 'En cours'
            ) {
                $sortie->setEtat($etats[3]);
            }
        }

        if ($dateLimiteHisto <= $now &&
            (
                $sortie->getEtat()->getnom() == 'Terminee' || $sortie->getEtat()->getnom() == 'Annulee')
        ) {
            $sortie->setEtat($etats[4]);
        }
    }
    $this->entityManager->flush();
}


    public function checkDevice(Request                $request)
    {
        $userAgent = $request->headers->get('User-Agent');
        if (preg_match('/Mobile|Android|iPhone|BlackBerry|IEMobile/i', $userAgent ?? '')) {
            throw  throw new NotFoundHttpException('Accès interdit sur mobile');
        }
    }




}
