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
        $this->inscrireParticipants($manager);
    }

    public function addCampus(ObjectManager $manager)
    {
        $campusList = ['Rennes', 'Nantes', 'Quimper', 'Niort'];

        foreach ($campusList as $element) {
            $campus = new Campus();
            $campus->setNom($element);

            $manager->persist($campus);
        }

        $manager->flush();
    }

    public function addEtats(ObjectManager $manager)
    {
        $etats = "En creation; Ouverte; Cloturee; En cours; Terminee; Annulee; Historisee";
        $etatList = explode("; ", $etats);

        foreach ($etatList as $element) {
            $etat = new Etat();
            $etat->setNom($element);

            $manager->persist($etat);
        }

        $manager->flush();
    }

    public function addVilles(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $villeList = array(
            '35131' => 'Chartres-de-Bretagne',
            '35170' => 'Bruz',
            '35000' => 'Rennes',
            '29000' => 'Quimper',
            '29200' => 'Brest',
            '79000' => 'Niort',
            '44000' => 'Nantes',
        );

        foreach ($villeList as $codePostal => $nom) {
            $ville = new Ville();
            $ville->setNom($nom);
            $ville->setCodePostal($codePostal);

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
                ->setLatitude($faker->randomFloat(4, -50, 50))
                ->setLongitude($faker->randomFloat(4, -50, 50));
            $manager->persist($lieu);
        }
        $manager->flush();
    }

    public function addUsers(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $campusList = $manager->getRepository(Campus::class)->findAll();
        $avatar = ['avatar_1.jpg','avatar_2.jpg','avatar_3.jpg'];

        $admin = new Participant();
        $admin
            ->setUsername('admin')
            ->setEmail('admin@admin.admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setCampus($campusList[0])
            ->setPhoto('admin.jpg')
            ->setPassword(
                $this->userPasswordHasher->hashPassword($admin, 'admin')
            );
        $manager->persist($admin);

        $userUser = new Participant();
        $userUser
            ->setUsername('user')
            ->setEmail('user@user.user')
            ->setRoles(['ROLE_USER'])
            ->setCampus($campusList[0])
            ->setPhoto('neo.jpg')
            ->setPassword(
                $this->userPasswordHasher->hashPassword($userUser, 'user')
            );
        $manager->persist($userUser);

        for ($i = 0; $i < 50; $i++) {
            $user = new Participant();
            $user
                ->setCampus($faker->randomElement($campusList))
                ->setPrenom($faker->firstName())
                ->setNom($faker->lastName())
                ->setTel($faker->phoneNumber())
                ->setRoles(['ROLE_USER'])
                ->setEmail($faker->email())
                ->setUsername($faker->userName())
                ->setPhoto($faker->randomElement($avatar))
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
            $organisateur = $faker->randomElement($participantList);
            $sortie
                ->setDateHeureDebut($faker->dateTimeBetween('-2 month', '+2 months', 'Europe/Paris'))
                ->setDateLimiteInscription($faker->dateTimeBetween($sortie->getDateHeureDebut()->modify('-14 days'), $sortie->getDateHeureDebut()->modify('-1 days'), 'Europe/Paris'))
                ->setDuree($faker->numberBetween(15, 180))
                ->setNbPlaces($faker->numberBetween(2, 40))
                ->setDescription($faker->realText(255))
                ->setOrganisateur($organisateur)
                ->sIncrire($organisateur)
                ->setCampus($faker->randomElement($campusList))
                ->setEtat($faker->randomElement($etatList))
                ->setLieu($faker->randomElement($lieuList))
                ->setNom($sortie->getLieu()->getNom() . ' - ' . $sortie->getOrganisateur()->getNom() . ' - ' . $sortie->getLieu()->getVille()->getNom());
            $manager->persist($sortie);
        }
        $manager->flush();
    }

    public function inscrireParticipants(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $participantList = $manager->getRepository(Participant::class)->findAll();
        $sortieList = $manager->getRepository(Sortie::class)->findAll();

        // Une boucle pour chaque sortie
        // Une boucle pour combien de participants mettre, avec un score de $i variable

        foreach ($sortieList as $sortie) {
            for ($i = 0; $i < $sortie->getNbPlaces(); $i++) {
                $sortie->sIncrire($faker->randomElement($participantList));
            }
            $manager->persist($sortie);
        }
        $manager->flush();
    }
}
