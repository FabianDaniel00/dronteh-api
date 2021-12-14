<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class JWTNotFoundListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        if ($this->requestStack->getCurrentRequest()->cookies->get('REFRESH_TOKEN')) {
            $data = [
                'code'  => Response::HTTP_UNAUTHORIZED,
                'message' => 'Expired JWT Token',
            ];
            $response = new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
            return $event->setResponse($response);
        }
        $data = [
            'code'  => Response::HTTP_UNAUTHORIZED,
            'message' => 'Missing JWT Refresh Token',
        ];
        $response = new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        return $event->setResponse($response);
    }
}