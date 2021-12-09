<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\RefreshTokenRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Firebase\JWT\JWT;

class JwtAuthenticator extends AbstractAuthenticator
{
    private $userRepository;
    private $refreshTokenRepository;
    private $params;
    private $security;
    private $newJwt = "";

    public function __construct(Security $security, UserRepository $userRepository, RefreshTokenRepository $refreshTokenRepository, ContainerBagInterface $params)
    {
        $this->userRepository = $userRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->security = $security;
        $this->params = $params;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('x-auth-token');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('x-auth-token');
        if (null === $apiToken || !str_contains($apiToken, 'Bearer ')) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        return new SelfValidatingPassport(
            new UserBadge($apiToken, function() use ($apiToken) {
                $user = new User();
                try {
                    $apiToken = str_replace('Bearer ', '', $apiToken);
                    $jwt = (array) JWT::decode($apiToken, $this->params->get('jwt_secret'), ['HS256']);

                    $user = $this->userRepository->findOneBy(['email' => $jwt['user']]);

                    if(!$user->isVerified()) {
                        throw new CustomUserMessageAuthenticationException('You are not verified, please verify your email.');
                    }

                } catch(\Exception $exception) {
                    try {
                        $refreshToken = $this->refreshTokenRepository->findOneBy(['email' => $this->security->getUser()]);
                        if(!$refreshToken) {
                            throw new CustomUserMessageAuthenticationException('No refresh token provided. Please re-login.');
                        }
                        (array) JWT::decode($refreshToken->getToken(), $this->params->get('refresh_jwt_secret'), ['HS256']);
                        $payloadNewToken = [
                            "user" => $user->getEmail(),
                            "exp" => (new \DateTime())->modify("+5 minutes")->getTimestamp(),
                        ];
                        $this->newJwt = JWT::encode($payloadNewToken, $this->params->get('jwt_secret'), 'HS256');
                    } catch(\Exception $exception) {
                        throw new CustomUserMessageAuthenticationException('Token expired. Please re-login.');
                    }
                }
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}