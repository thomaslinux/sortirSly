<?php

namespace App\DataFixtures;

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
        $this->addUsers($manager);
    }

    public function addUsers(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $user = new Participant();
            $user
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
