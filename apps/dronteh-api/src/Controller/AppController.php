<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('app/home.html.twig', []);
    }

    /**
     * @Route("/specializations", name="app_specializations")
     */
    public function specializations(): Response
    {
        return $this->render('app/specializations.html.twig', []);
    }

    /**
     * @Route("/team", name="app_team")
     */
    public function team(): Response
    {
        return $this->render('app/team.html.twig', []);
    }
}
