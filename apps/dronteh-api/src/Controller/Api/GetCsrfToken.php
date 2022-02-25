<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetCsrfToken extends AbstractController
{
    /**
     * @Route("/get_csrf_token", name="api_get_csrf_token", methods="GET")
     */
    public function getCsrfToken(): Response
    {
        $token = $this->container->get('security.csrf.token_manager')->refreshToken($this->getParameter('csrf_token_id'))->getValue();

        return $this->json([
            'data' => [
                'csrf_token' => $token
            ]
        ]);
    }
}
