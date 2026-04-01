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
            return $etat->findOneBy(["nom" => "Cloturee"]);
        }
        if ($sortie->getInscriptions()->count() < $sortie->getNbPlaces()) {
            return $etat->findOneBy(["nom" => "Ouverte"]);
        }
        if ($sortie->getDateLimiteInscription() < $now) {
            return $etat->findOneBy(["nom" => "Cloturee"]);
        }

        return $etat->findOneBy(["nom" => "Ouverte"]);
    }
}
