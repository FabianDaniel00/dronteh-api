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
     * @Route("/verify/email/{_locale}", name="app_users_verify_email", methods="GET")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EmailVerifier $emailVerifier, TranslatorInterface $translator, string $_locale): Response
    {
        if ($_locale && in_array($_locale, $this->getParameter('app.supported_locales'))) {
            $request->setLocale($_locale);
        }

        $id = $request->get('id');

        if (null === $id) {
            return new Response($translator->trans('app.verify_user_email.id_null', [], 'app'), Response::HTTP_FORBIDDEN);
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return new Response($translator->trans('app.verify_user_email.user_null', [], 'app'), Response::HTTP_FORBIDDEN);
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return new Response($translator->trans('app.verify_user_email.errors.'.$exception->getReason(), [], 'app'), Response::HTTP_FORBIDDEN);
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        return new Response(
            $translator->trans('app.verify_user_email.success.message', [], 'app').' <a href="'.$this->getParameter('client_side_host').'/auth?success_verification=true&locale='.$request->getLocale().'">'.$translator->trans('app.verify_user_email.success.click', [], 'app').'</a>.'
        );
    }
}