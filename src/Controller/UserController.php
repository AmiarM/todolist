<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserController extends AbstractController
{
    /**
     *
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     *
     * @var UserRepository
     */
    private $repository;
    /**
     *
     * @var AuthorizationCheckerInterface
     */
    private $checker;

    public function __construct(AuthorizationCheckerInterface $checker, EntityManagerInterface $manager, UserRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->checker = $checker;
    }
    /**
     * @Route(path="/users", name="user_list")
     */
    public function listAction(AuthorizationCheckerInterface $checker, PaginatorInterface $paginator, Request $request)
    {
        $user = $this->getUser();
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'access denieded'], Response::HTTP_UNAUTHORIZED);
        }
        $users = $this->repository->findAll();
        $pagination = $paginator->paginate(
            $users, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12  /*limit per page*/
        );
        return $this->render('user/list.html.twig', [
            'users' => $pagination
        ]);
    }

    /**
     * @Route(path="/users/create", name="user_create")
     */
    public function createAction(Request $request, UserPasswordHasherInterface $encoder)
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'access denieded'], Response::HTTP_UNAUTHORIZED);
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->manager->persist($user);
            $this->manager->flush();
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");
            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route(path="/users/{id}/edit", name="user_edit")
     */
    public function editAction(User $user, Request $request, UserPasswordHasherInterface $encoder)
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'access denieded'], Response::HTTP_UNAUTHORIZED);
        }
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->manager->persist($user);
            $this->manager->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
