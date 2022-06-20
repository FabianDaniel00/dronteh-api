<?php

namespace App\Controller;

use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class VerifyUserEmailController extends AbstractController
{
    /**
     * @Route("/verify/email", name="app_users_verify_email", methods="GET")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EmailVerifier $emailVerifier, TranslatorInterface $translator): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            $this->addFlash('danger', $this->translator->trans('app.verify_user_email.id_null', [], 'app'));
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            $this->addFlash('danger', $this->translator->trans('app.verify_user_email.user_null', [], 'app'));
        }

        if ($user->isVerified()) {
            $this->addFlash('danger', $this->translator->trans('app.verify_user_email.is_verified', [], 'app'));
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $emailVerifier->handleEmailConfirmation($request, $user);

            $this->addFlash('success', $this->translator->trans('app.verify_user_email.success.message', [], 'app'));
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $this->translator->trans('app.verify_user_email.errors.'.$exception->getReason(), [], 'app'));
        }

        return $this->redirectToRoute('app_login');
    }
}
