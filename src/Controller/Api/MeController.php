<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\jsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MeController extends AbstractController
{
    /**
    * @Route("/me", name="api_me", methods={"GET"})
    */
    public function index(): JsonResponse
    {
        return $this->json([
            'current_user' => $this->getUser()
        ]);
    }
}