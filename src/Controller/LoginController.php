<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use App\Repository\UserRepository;
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
    public function login(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
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
            $payload = [
                "user" => $user->getEmail(),
                "exp" => (new \DateTime())->modify("+5 minutes")->getTimestamp(),
            ];
    
            $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');
    
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