<?php

namespace App\Controller;


use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultController extends WebTestCase
{
    use FixturesTrait;
    /**
     * @Route(path="/", name="homepage", methods={"GET"})
     */
    public function indexAction()
    {
        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        var_dump($users);
        // return $this->render('default/index.html.twig');
    }
}
