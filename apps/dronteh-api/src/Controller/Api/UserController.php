<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\JsonApi\Document\User\UserDocument;
use App\JsonApi\Document\User\UsersDocument;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\JsonApi\Hydrator\User\CreateUserHydrator;
use App\JsonApi\Hydrator\User\UpdateUserHydrator;
use Paknahad\JsonApiBundle\Controller\Controller;
use App\JsonApi\Transformer\UserResourceTransformer;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="api_users_index", methods="GET")
     */
    public function index(UserRepository $userRepository, ResourceCollection $resourceCollection): Response
    {
        $resourceCollection->setRepository($userRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new UsersDocument(new UserResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="api_users_new", methods="POST")
     */
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EmailVerifier $emailVerifier, TranslatorInterface $translator): Response
    {
        if (!$this->isCsrfTokenValid($this->getParameter('csrf_token_id'), $request->headers->get('x-csrf-token'))) {
            // throw new AccessDeniedHttpException('api.users.invlaid_csrf_token');
        }

        $user = $this->jsonApi()->hydrate(
            new CreateUserHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new User()
        );

        $user->setCaptcha($request->headers->get('x-captcha'));

        $this->validate($user);

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $emailVerifier->sendEmailConfirmation('api_users_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->getParameter('sender_email'), $translator->trans('mails.register_confirmation.name', [], 'api')))
                ->to($user->getEmail())
                ->subject($translator->trans('mails.register_confirmation.subject', [], 'api'))
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->respondOk(
            new UserDocument(new UserResourceTransformer()),
            $user
        );
    }

    /**
     * @Route("/{id}", name="api_users_show", methods="GET")
     */
    public function show(User $user): Response
    {
        if ($user->isDeleted()) {
            throw $this->createNotFoundException('api.users.show.is_deleted');
        }

        return $this->respondOk(
            new UserDocument(new UserResourceTransformer()),
            $user
        );
    }

    /**
     * @Route("/{id}", name="api_users_edit", methods="PATCH")
     */
    public function edit(User $user): Response
    {
        if ($user->isDeleted()) {
            throw $this->createNotFoundException('api.users.show.is_deleted');
        }

        $user = $this->jsonApi()->hydrate(
            new UpdateUserHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            $user
        );

        $this->validate($user);

        $this->entityManager->flush();

        return $this->respondOk(
            new UserDocument(new UserResourceTransformer()),
            $user
        );
    }

    /**
     * @Route("/{id}", name="api_users_delete", methods="DELETE")
     */
    public function delete(User $user): Response
    {
        if ($user->isDeleted()) {
            throw $this->createNotFoundException('api.users.show.is_deleted');
        }

        $user->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }

    /**
     * @Route("/{_locale}/verify/email", name="api_users_verify_email", methods="GET")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EmailVerifier $emailVerifier, TranslatorInterface $translator, $_locale): Response
    {
        $request->setLocale($_locale);

        $id = $request->get('id');

        if (null === $id) {
            return new Response($translator->trans('api.users.verify_email.id_null', [], 'api'), Response::HTTP_FORBIDDEN);
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return new Response($translator->trans('api.users.verify_email.user_null', [], 'api'), Response::HTTP_FORBIDDEN);
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            return new Response($translator->trans('api.users.verify_email.error', [], 'api').$exception->getReason(), Response::HTTP_FORBIDDEN);
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        return new Response(
            $translator->trans('api.users.verify_email.success.message', [], 'api').' <a href="'.$this->getParameter('client_side_host').'/auth?success_verification=true">'.$translator->trans('api.users.verify_email.success.click', [], 'api').'</a>'
        );
    }
}