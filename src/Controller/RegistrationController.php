<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/api/auth/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $request->request->all();

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('api_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('dronteh.confirm@gmail.com', 'DronTeh Confirm'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->json(['message' => 'Registration was successful! Verification email was sent.']);
        }

        return $this->json(['form' => $form]);
    }

    /**
     * @Route("api/verify/email", name="api_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): JsonResponse
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->json(['message' => "Can't find the user ID!"]);
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->json(['message' => "Can't find the user!"]);
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->json(['message' => 'Something went wrong. Error: '.$exception->getReason()]);
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        return $this->json(['message' => 'Successfully verified.']);
    }
}