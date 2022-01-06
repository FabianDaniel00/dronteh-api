<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?JsonResponse
    {
        return new JsonResponse([
            'code' => Response::HTTP_FORBIDDEN,
            'message' => 'This content in not available for you!'
        ], Response::HTTP_FORBIDDEN);
    }
}