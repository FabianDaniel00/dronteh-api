<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('api_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($this->getParameter('sender_email'), 'DronTeh Confirm'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->json([
                'code' => Response::HTTP_OK,
                'message' => 'Registration was successful! Verification email was sent.'
            ]);
        }

        return $this->json([
            'code' => Response::HTTP_FORBIDDEN,
            'form' => $form
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @Route("/verify/email", name="api_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return new Response("Can't find the user ID!", Response::HTTP_FORBIDDEN);
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return new Response("Can't find the user!", Response::HTTP_FORBIDDEN);
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return new Response('Something went wrong. '.$exception->getReason(), Response::HTTP_FORBIDDEN);
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        return new Response(
            'Your email have been successfully verified. <a href="'.$this->getParameter('client_side_host').'/auth?success_verification=true">Click here to login</a>',
            Response::HTTP_OK
        );
    }
}