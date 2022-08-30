<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($nbUsers = 1; $nbUsers <= 5; $nbUsers++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword($this->encoder->hashPassword($user, 'password'));
            $user->setUsername($faker->word(1));
            // Enregistre l'utilisateur dans une référence
            $this->addReference('user_' . $nbUsers, $user);
            if ($nbUsers === 1) {
                $user->setRoles(['ROLE_ADMIN']);
                //$user->setPassword($this->encoder->hashPassword($user, 'password'));
            } else if ($nbUsers === 2) {
                $user->setRoles(['ROLE_ANONYMOUS']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }
            $manager->persist($user);
        }

        $manager->flush();
    }
}
