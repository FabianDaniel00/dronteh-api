<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\RefreshToken;
use App\Form\LoginFormType;
use App\Repository\UserRepository;
use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Firebase\JWT\JWT;

class LoginController extends AbstractController
{
    /**
     * @Route("/api/auth/login", name="api_login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, RefreshTokenRepository $refreshTokenRepository, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();

        $user = new User();
        $form = $this->createForm(LoginFormType::class, $user);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy([
                'email'=>$request->get('email'),
            ]);
            if (!$user || !$userPasswordHasher->isPasswordValid($user, $request->get('password'))) {
                return $this->json([
                    'message' => 'Wrong email or password.',
                ]);
            }
            $payloadToken = [
                "user" => $user->getEmail(),
                "exp" => (new \DateTime())->modify("+5 minutes")->getTimestamp(),
            ];
            $payloadRefreshToken = [
                "user" => $user->getEmail(),
                "exp" => (new \DateTime())->modify("+2 weeks")->getTimestamp(),
            ];
    
            $jwt = JWT::encode($payloadToken, $this->getParameter('jwt_secret'), 'HS256');
            $refreshJwt = JWT::encode($payloadRefreshToken, $this->getParameter('refresh_jwt_secret'), 'HS256');

            $refreshToken = $refreshTokenRepository->findOneBy(['email'=>$user->getEmail()]);

            if($refreshToken) {
                $refreshToken->setToken($refreshJwt);
            } else {
                $refreshToken = new RefreshToken();
                $refreshToken->setToken($refreshJwt);
                $refreshToken->setEmail($user->getEmail());
                $entityManager->persist($refreshToken);
            }
            $entityManager->flush();

            return $this->json([
                'message' => 'Success!',
                'token' => sprintf('Bearer %s', $jwt),
            ], Response::HTTP_OK);
        }

        return $this->json(['form' => $form]);
    }

    /**
     * @Route("api/logout", name="api_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}