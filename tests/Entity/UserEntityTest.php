<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends KernelTestCase
{
    //---------------------------------user valid----------------------------
    public function testValidUser()
    {
        $user = $this->getCurrentUser();
        $this->assertHasErrors($user, 0);
    }
    //---------------getCurrentUser()----------------------------------------
    private function getCurrentUser(): User
    {
        $task = new Task();
        $task->setTitle("title")
            ->setContent("content");
        return (new User())
            ->setUsername("username")
            ->setEmail("test@test.com")
            ->setPassword("password")
            ->addTask($task);
    }
    //---------------assertHasErrors-----------------------------------------
    private function assertHasErrors(User $user, int $count = 0)
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        /**
         * @var ConstraintViolationList
         */
        $errors = $validator->validate($user);
        $messages = [];
        /**
         * @var ConstraintViolation $error
         */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . '=>' . $error->getMessage();
        }
        $this->assertCount($count, $errors, implode(',', $messages));
    }
    //---------------------invalide Email-------------------------------------
    public function testInvalidEmailUser()
    {
        $user = $this->getCurrentUser();
        $user->setEmail("1aaa123");
        $this->assertHasErrors($user, 1);
    }
}
