<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Doctrine\ORM\EntityManager;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TaskController extends AbstractController
{
    /**
     *
     * @var TaskRepository
     */
    private $repository;
    /**
     *
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(TaskRepository $repository, PaginatorInterface $paginator)
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
    }
    /**
     * @Route(path="/tasks", name="task_list_enabling_false")
     */
    public function listEnablingTrueAction(AuthorizationCheckerInterface $checker, Request $request)
    {
        $user = $this->getUser();
        if ($checker->isGranted("ROLE_ADMIN")) {
            $tasks = $this->repository->findBy(['isDone' => false]);
            $pagination = $this->paginate($tasks, $request);
            return $this->render('task/list.html.twig', [
                'tasks' => $pagination
            ]);
        }
        $tasks = $this->repository->findBy(['user' => $user, 'isDone' => false], ['createdAt' => 'desc']);
        $pagination = $this->paginate($tasks, $request);
        return $this->render('task/list.html.twig', [
            'tasks' => $pagination
        ]);
    }
    /**
     * @Route(path="/tasks/Enabling/true", name="task_list_enabling_true")
     */
    public function listEnablingFalseAction(AuthorizationCheckerInterface $checker, Request $request)
    {
        $user = $this->getUser();
        if ($checker->isGranted("ROLE_ADMIN")) {
            $tasks = $this->repository->findBy(['isDone' => true]);
            $pagination = $this->paginate($tasks, $request);
            return $this->render('task/list.html.twig', [
                'tasks' => $pagination
            ]);
        }
        $tasks = $this->repository->findBy(['user' => $user, 'isDone' => true], ['createdAt' => 'desc']);
        $pagination = $this->paginate($tasks, $request);
        return $this->render('task/list.html.twig', [
            'tasks' => $pagination
        ]);
    }

    /**
     * @Route(path="/tasks/create", name="task_create",methods={"GET","POST"})
     */
    public function createAction(Request $request, EntityManagerInterface $manager)
    {
        /**
         * @var User
         */
        $user = $this->getUser();
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $task->setUser($user);
            $manager->persist($task);
            $manager->flush();
            $this->addFlash('success', 'La t??che a ??t?? bien ??t?? ajout??e.');
            return $this->redirectToRoute('task_list_enabling_false');
        }
        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route(path="/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $manager->flush();
            $this->addFlash('success', 'La t??che a bien ??t?? modifi??e.');
            return $this->redirectToRoute('task_list_enabling_false');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route(path="/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $task->toggle(!$task->isDone());
        $manager->flush();
        if ($task->isDone() == false) {
            $this->addFlash('success', sprintf('La t??che %s a bien ??t?? marqu??e comme non faite.', $task->getTitle()));
            return $this->redirectToRoute('task_list_enabling_false');
        } else {
            $this->addFlash('success', sprintf('La t??che %s a bien ??t?? marqu??e comme  faite.', $task->getTitle()));
            return $this->redirectToRoute('task_list_enabling_true');
        }
    }

    /**
     * @Route(path="/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task, EntityManagerInterface $manager)
    {
        if ($this->getUser() !== $task->getUser()) {
            if ($task->getUser()->getId() === -1) {
                if (!$this->isGranted('ROLE_ADMIN')) {
                    $this->addFlash('error', 'Seul un admin peut supprimer une t??che de l\'utilisateur anonyme !');
                    return $this->redirectToRoute('task_list');
                }
            }

            if (!$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('error', 'Seul l\'auteur de la t??che ou un admin peut la supprimer !');
                return $this->redirectToRoute('task_list');
            }
        }
        $manager->remove($task);
        $manager->flush();
        $this->addFlash('success', 'La t??che a bien ??t?? supprim??e.');

        return $this->redirectToRoute('task_list_enabling_false');
    }

    private function paginate(array $datas, Request $request)
    {
        $pagination = $this->paginator->paginate(
            $datas, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12  /*limit per page*/
        );
        return $pagination;
    }
}
