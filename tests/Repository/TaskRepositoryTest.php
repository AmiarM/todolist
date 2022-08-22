<?php

namespace App\Tests\Repository;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{


    public function testCount()
    {
        self::bootKernel();
        $users = self::getContainer()->get(TaskRepository::class)->count([]);
        $this->assertEquals(400, $users);
    }
}
