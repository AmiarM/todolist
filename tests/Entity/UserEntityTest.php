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
    //---------------------invalide Email-------------------------------------
    public function testInvalidEmailUser()
    {
        $user = $this->getCurrentUser();
        $user->setEmail("1aaa123");
        $this->assertHasErrors($user, 1);
    }
    //------------------------NotBlank----------------------------------------
    public function testInvalidNotBlankEmailUser()
    {
        $user = $this->getCurrentUser();
        $user->setEmail("");
        $this->assertHasErrors($user, 1);
    }

    public function testInvalidNotBlankUsernameUser()
    {
        $user = $this->getCurrentUser();
        $user->setUsername("");
        $this->assertHasErrors($user, 1);
    }
    public function testInvalidNotBlankPasswordUser()
    {
        $user = $this->getCurrentUser();
        $user->setPassword("");
        $this->assertHasErrors($user, 1);
    }
    //----------------------------email unique--------------------------------
    public function testInvalidUsedEmail()
    {
        $user = $this->getCurrentUser();
        $user->setEmail("admin@admin.com");
        $this->assertHasErrors($user, 1);
    }
    //------------------------------------------------------------------------
    private function getCurrentUser(): User
    {
        $task = new Task();
        $task->setTitle("title")
            ->setContent('content');
        return (new User())
            ->setUsername("username")
            ->setEmail("test@test.com")
            ->setPassword("password")
            ->addTask($task);
    }

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

    //-------------------------------------------------------------------------
    public function testUsername(): void
    {
        $user = $this->getCurrentUser();
        $this->assertSame('username', $user->getUsername());
    }

    public function testEmail(): void
    {
        $user = $this->getCurrentUser();
        $this->assertSame('test@test.com', $user->getEmail());
    }
    public function testRoles(): void
    {
        $user = $this->getCurrentUser();
        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }
    public function testSalt(): void
    {
        $user = $this->getCurrentUser();
        $this->assertNull($user->getSalt());
    }
    public function testPassword(): void
    {
        $user = $this->getCurrentUser();
        $this->assertSame('password', $user->getPassword());
    }

    public function testEraseCredentials(): void
    {
        $user = $this->getCurrentUser();
        $this->assertNull($user->eraseCredentials());
    }
    public function testTask()
    {
        $user = $this->getCurrentUser();
        foreach ($user->getTasks() as $task) {
            $user->addTask($task);
        }
        $this->assertCount(1, $user->getTasks());
        $tasks = $user->getTasks();
        $this->assertSame($user->getTasks(), $tasks);
        $user->removeTask($task);
        $this->assertCount(0, $user->getTasks());
    }
}
