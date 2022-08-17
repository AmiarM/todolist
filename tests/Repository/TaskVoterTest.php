<?php

namespace App\Tests\Security;

use App\Entity\Task;
use App\Entity\User;
use App\Security\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class TaskVoterTest extends WebTestCase
{
    private $task;

    private $voter;

    public function setUp(): void
    {
        $this->task = new Task();
        $this->voter = new TaskVoter();
    }

    private function createUser(int $id): User
    {
        $user = new User();
        $user->setId($id);
        $user->setRoles(['ROLE_USER']);

        return $user;
    }

    private function createUserRoles(int $id, string $roles): User
    {
        $user = new User();
        $user->setId($id);
        $user->setRoles([$roles]);

        return $user;
    }

    private function createTask($user = null): Task
    {
        $task = new Task();
        $task->setUser($user);

        return $task;
    }

    public function provideCases(): ?\Generator
    {

        yield 'Admin peut supprimer n\'importe quelle tache' => [
            $this->createUserRoles(2, 'ROLE_ADMIN'),
            $task = $this->createTask($this->createUser(3)),
            $attribute = 'edit',
            TaskVoter::ACCESS_GRANTED,
        ];

        yield 'l\'admin peut supprimer une tache annonyme' => [
            $this->createUserRoles(2, 'ROLE_ADMIN'),
            $task = $this->createTask(Null),
            $attribute = 'delete',
            TaskVoter::ACCESS_GRANTED,
        ];

        yield 'User peut supprimer sa tache' => [
            $user = $this->createUser(2),
            $task = $this->createTask($user),
            $attribute = 'edit',
            TaskVoter::ACCESS_GRANTED,
        ];

        yield 'User ne peut pas supprimer une task d\' une autre utilisateur ' => [
            $this->createUser(2),
            $task = $this->createTask($this->createUser(4)),
            $attribute = 'edit',
            TaskVoter::ACCESS_DENIED,
        ];

        yield 'Utilisateur annonyme ne peut pas supprimer' => [
            $user = null,
            $task = $this->createTask(null),
            $attribute = 'delete',
            TaskVoter::ACCESS_DENIED,
        ];
    }

    /**
     * @dataProvider provideCases
     * @param $user
     * @param Task $task
     * @param string $attribute
     * @param int $expectedVote
     */
    public function testVote(
        $user,
        Task $task,
        string $attribute,
        int $expectedVote): void
    {

        $token = new AnonymousToken('secret', 'anonymous');
        if ($user) {
            $token = new UsernamePasswordToken(
                $user, 'password', 'memory'
            );
        }

        $this->assertSame($expectedVote, $this->voter->vote($token, $task, [$attribute]));
    }
}