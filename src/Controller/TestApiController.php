<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\jsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestApiController extends AbstractController
{
    /**
    * @Route("/api/test", name="testapi", methods={"GET"})
    */
    public function test(): JsonResponse
    {
        return $this->json([
            'message' => 'test!',
            'user' => $this->getUser()
        ]);
    }
}