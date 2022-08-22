<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\DBAL\Schema\Constraint;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskEntityTest extends KernelTestCase
{
    //------------------Task Valid------------------------------------------
    public function testValidTask()
    {
        $task = $this->getTask();
        $this->assertHasErrors($task, 0);
    }
    //----------------NotBlank----------------------------------------------
    public function testInvalidNotBlankContentTask()
    {
        $task = $this->getTask();
        $task->setContent("");
        $this->assertHasErrors($task, 1);
    }

    public function testInvalidNotBlankTitleTask()
    {
        $task = $this->getTask();
        $task->setTitle("");
        $this->assertHasErrors($task, 1);
    }
    //------------------------------------------------------------------------
    private function getTask(): Task
    {
        return (new Task())
            ->setTitle("title")
            ->setContent("content");
    }
    private function assertHasErrors(Task $task, int $count = 0)
    {
        self::bootKernel();
        $validator = self::getContainer()->get(ValidatorInterface::class);
        /**
         * @var ConstraintViolationList
         */
        $errors = $validator->validate($task);
        $messages = [];
        /**
         *@var ConstraintViolation $error
         */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . "=>" . $error->getMessage();
        }
        $this->assertCount($count, $errors, implode(',', $messages));
    }
    //-------------------------------------------------------------------------
    public function testTitle(): void
    {
        $task = $this->getTask();
        $this->assertSame('title', $task->getTitle());
    }

    public function testContent(): void
    {
        $task = $this->getTask();
        $this->assertSame('content', $task->getContent());
    }
}
