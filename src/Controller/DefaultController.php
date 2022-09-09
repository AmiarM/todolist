<?php

namespace App\Controller;


use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    use FixturesTrait;
    /**
     * @Route(path="/", name="homepage", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}
