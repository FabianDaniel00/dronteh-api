<?php

namespace App\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\jsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MeController extends AbstractController
{
    /**
    * @Route("/me", name="api_me", methods={"GET"})
    */
    public function index(): JsonResponse
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException('api.current_user.null');
        }

        return $this->json([
            'data' => [
                'current_user' => $currentUser
            ]
        ]);
    }
}
