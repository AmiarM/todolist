<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{

    /**
     *
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    /**
     * @Route(path="/register", name="user_register")
     */
    public function createAction(Request $request, UserPasswordHasherInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->manager->persist($user);
            $this->manager->flush();
            $this->addFlash('success', "Superbe ! votre inscription s'est déroulée avec succès.");
            return $this->redirectToRoute('login_check');
        }
        return $this->render('register/register.html.twig', ['form' => $form->createView()]);
    }
}
