<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

/**
 * @Route("/reset_password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private ResetPasswordHelperInterface $resetPasswordHelper;
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("/", name="app_forgot_password_request", methods={"GET", "POST"})
     */
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer,
                $resetToken->getExpirationMessageData()
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_reset_password", methods={"GET", "POST"})
     */
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, string $token = null): ?Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            $this->addFlash('danger', $this->translator->trans('app.reset_password.request.errors.token_null', [], 'app'));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('danger', $this->translator->trans('app.reset_password.request.errors.'.$e->getReason(), [], 'app'));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            $this->addFlash('success', $this->translator->trans('app.reset_password.reset.success', [], 'app'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, $expiresAt): RedirectResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            $this->addFlash('success', $this->translator->trans('app.reset_password.request.success', ['%count%' => $expiresAt], 'app'));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);

            $email = (new TemplatedEmail())
                ->from(new Address($this->getParameter('sender_email'), $this->translator->trans('mails.reset_password.name', [], 'mails')))
                ->to($user->getEmail())
                ->subject($this->translator->trans('mails.reset_password.subject', [], 'mails'))
                ->htmlTemplate('reset_password/email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                    'name' => $user->getFirstname().' '.$user->getLastname(),
                ])
            ;

            $mailer->send($email);

            $this->addFlash('success', $this->translator->trans('app.reset_password.request.success', ['%count%' => $expiresAt], 'app'));
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('danger', $this->translator->trans('app.reset_password.request.errors.'.$e->getReason(), [], 'app'));
        }

        return $this->redirectToRoute('app_forgot_password_request');
    }
}
