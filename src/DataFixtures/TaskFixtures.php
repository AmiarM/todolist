<?php

namespace App\DataFixtures;


use Faker;
use App\Entity\Task;
use App\DataFixtures\UserFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($nbTask = 1; $nbTask <= 5; $nbTask++) {
            $user = $this->getReference('user_' . $faker->numberBetween(1, 5));

            $task = new Task();
            $task->setTitle($faker->word(2))
                ->setContent($faker->paragraph())
                ->setUser($user);
            $manager->persist($task);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
