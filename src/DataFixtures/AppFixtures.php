<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->addCampus($manager);
        $this->addEtats($manager);
        $this->addVilles($manager);
        $this->addLieux($manager);
        $this->addUsers($manager);
        $this->addSorties($manager);
    }
    public function addCampus(ObjectManager $manager) {
        $campusList = ['Rennes', 'Nantes', 'Quimper', 'Niort'];

        foreach ($campusList as $element) {
            $campus = new Campus();
            $campus->setNom($element);

            $manager->persist($campus);
        }

        $manager->flush();
    }

    public function addEtats(ObjectManager $manager) {
        $etats = "En creation; Ouverte; Cloturee; En cours; Terminee; Annulee; Historisee";
        $etatList = explode("; ", $etats);

        foreach ($etatList as $element) {
            $etat = new Etat();
            $etat->setNom($element);

            $manager->persist($etat);
        }

        $manager->flush();
    }

    public function addVilles(ObjectManager $manager) {
        $faker = Factory::create('fr_FR');

        $villes = "Chartres-de-Bretagne; Bruz; Rennes; Quimper; Brest; Niort; Nantes";
        $villeList = explode("; ", $villes);

        foreach ($villeList as $element) {
            $ville = new Ville();
            $ville->setNom($element);
            $ville->setCodePostal($faker->numberBetween(10000,99999));

            $manager->persist($ville);
        }

        $manager->flush();
    }

    public function addLieux(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $lieux = "Bar; Patinoire; Parc; FNAC; Carrefour; Lidl; Cafe; Universite";
        $lieuList = explode("; ", $lieux);
        $villeList = $manager->getRepository(Ville::class)->findAll();

        foreach ($lieuList as $element) {

            $lieu = new Lieu();
            $lieu
                ->setNom($element)
                ->setVille($faker->randomElement($villeList))
            ;

            $manager->persist($lieu);
        }

        $manager->flush();
    }

    public function addUsers(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $campusList = $manager->getRepository(Campus::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $user = new Participant();
            $user
                ->setCampus($faker->randomElement($campusList))
                ->setPrenom($faker->firstName())
                ->setNom($faker->lastName())
                ->setTel($faker->phoneNumber())
                ->setRoles(['ROLE_USER'])
                ->setEmail($faker->email())
                ->setUsername($faker->userName())
                ->setPassword(
                    $this->userPasswordHasher->hashPassword($user, '123456')
                );
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function addSorties(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $participantList = $manager->getRepository(Participant::class)->findAll();
        $campusList = $manager->getRepository(Campus::class)->findAll();
        $etatList = $manager->getRepository(Etat::class)->findAll();
        $lieuList = $manager->getRepository(Lieu::class)->findAll();

        for ($i = 0; $i < 200; $i++) {
            $sortie = new Sortie();
            $sortie
                ->setNom($faker->text(15))
                ->setDateHeureDebut($faker->dateTimeBetween('now','+2 months', 'Europe/Paris'))
                ->setDateLimiteInscription($faker->dateTimeBetween('now', $sortie->getDateHeureDebut(),'Europe/Paris'))
                ->setDuree($faker->numberBetween(15, 180))
                ->setNbPlaces($faker->numberBetween(2,255))
                ->setDescription($faker->text(255));
            $sortie
                ->setOrganisateur($faker->randomElement($participantList))
                ->setCampus($faker->randomElement($campusList))
                ->setEtat($faker->randomElement($etatList))
                ->setLieu($faker->randomElement($lieuList));
            $manager->persist($sortie);
        }
        $manager->flush();
    }

//    public function inscrireParticipants(ObjectManager $manager)
//    {
//
//    }
}
