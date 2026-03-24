<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
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
        $this->addEtat($manager);
        $this->addUsers($manager);
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

    public function addEtat(ObjectManager $manager) {
        $etats = "En creation; Ouverte; Cloturee; En cours; Terminee; Annulee; Historisee";
        $etatList = explode("; ", $etats);

        foreach ($etatList as $element) {
            $etat = new Etat();
            $etat->setNom($element);

            $manager->persist($etat);
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



}
