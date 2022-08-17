<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $admin = new User();
        $admin->setUsername($faker->word(1))
            ->setPassword($this->hasher->hashPassword($admin, "password"))
            ->setEmail('admin@admin.com')
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);
        for ($u = 0; $u < 20; $u++) {
            $user = new User();
            $user->setPassword($this->hasher->hashPassword($user, "password"))
                ->setUsername($faker->word(1))
                ->setEmail($faker->email());

            $manager->persist($user);

            for ($t = 0; $t < 20; $t++) {
                $task = new Task();
                $task->setTitle($faker->word(2))
                    ->setContent($faker->paragraph())
                    ->setUser($user);
                $manager->persist($task);
            }
        }
        $manager->flush();
    }
}
