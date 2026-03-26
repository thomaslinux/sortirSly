<?php

namespace App\Service;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;

class EtatService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function checkEtat(Sortie $sortie)
    {
        $etat = $this->entityManager->getRepository(Etat::class);
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));


        if ($sortie->getInscriptions()->count() >= $sortie->getNbPlaces()) {
            return $etat->find(3); //Cloturee
        }
        if ($sortie->getInscriptions()->count() < $sortie->getNbPlaces()) {
            return $etat->find(2); //Ouverte
        }
        if ($sortie->getDateLimiteInscription() < $now) {
            return $etat->find(3); //Cloturee
        }

        return $etat->find(2);
    }
}
