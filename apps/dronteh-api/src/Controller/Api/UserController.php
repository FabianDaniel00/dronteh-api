<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Security\EmailVerifier;
// use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\JsonApi\Document\User\UserDocument;
// use App\JsonApi\Document\User\UsersDocument;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\JsonApi\Hydrator\User\CreateUserHydrator;
use App\JsonApi\Hydrator\User\UpdateUserHydrator;
use Paknahad\JsonApiBundle\Controller\Controller;
use App\JsonApi\Transformer\UserResourceTransformer;
// use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    // /**
    //  * @Route("/", name="api_users_index", methods="GET")
    //  */
    // public function index(UserRepository $userRepository, ResourceCollection $resourceCollection): Response
    // {
    //     $resourceCollection->setRepository($userRepository);

    //     $resourceCollection->getQuery()->where('r.is_deleted = 0');
    //     $resourceCollection->handleIndexRequest();

    //     return $this->respondOk(
    //         new UsersDocument(new UserResourceTransformer()),
    //         $resourceCollection
    //     );
    // }

    /**
     * @Route("/", name="api_users_new", methods="POST")
     */
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EmailVerifier $emailVerifier, TranslatorInterface $translator): Response
    {
        if (!$this->isCsrfTokenValid($this->getParameter('csrf_token_id'), $request->headers->get('x-csrf-token'))) {
            // throw new AccessDeniedHttpException('api.users.new.invlaid_csrf_token');
        }

        $user = $this->jsonApi()->hydrate(
            new CreateUserHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new User($request->headers->get('x-captcha'), $this->getParameter('app.supported_locales'), $this->getParameter('app.supported_roles'))
        );

        $this->validate($user);

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $emailVerifier->sendEmailConfirmation('app_users_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->getParameter('sender_email'), $translator->trans('mails.register_confirmation.name', [], 'mails')))
                ->to($user->getEmail())
                ->subject($translator->trans('mails.register_confirmation.subject', [], 'mails'))
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'name' => $user->getFirstname().' '.$user->getLastname(),
                ])
        );

        return $this->respondOk(
            new UserDocument(new UserResourceTransformer()),
            $user
        );
    }

    // /**
    //  * @Route("/{id}", name="api_users_show", methods="GET")
    //  */
    // public function show(User $user): Response
    // {
    //     if ($user->isDeleted()) {
    //         throw $this->createNotFoundException('api.users.not_found');
    //     }

    //     return $this->respondOk(
    //         new UserDocument(new UserResourceTransformer()),
    //         $user
    //     );
    // }

    /**
     * @Route("/{id}", name="api_users_edit", methods="PATCH")
     */
    public function edit(User $user): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException('api.current_user.null');
        }

        if ($user->isDeleted() || $user->getId() !== $currentUser->getId()) {
            throw $this->createNotFoundException('api.users.not_found');
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
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException('api.current_user.null');
        }

        if ($user->isDeleted() || $user->getId() !== $currentUser->getId()) {
            throw $this->createNotFoundException('api.users.not_found');
        }

        $user->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }

    /**
     * @Route("/ask_another_user_verification_email/{user_id}", name="api_users_ask_another_user_verification_email", methods="GET")
     */
    public function askAnotherUserVerificationEmail(EmailVerifier $emailVerifier, TranslatorInterface $translator, int $user_id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($user_id);

        if ($user->isDeleted()) {
            throw $this->createNotFoundException('api.users.not_found');
        }

        if ($user->isVerified()) {
            throw new AccessDeniedHttpException('api.users.is_verified');
        }

        if ($user->getLastVerificationEmailSent()->modify('+10 minutes') > new \DateTime('@'.strtotime('now'))) {
            throw new AccessDeniedHttpException('api.users.ask_another_user_verification_email.treshold');
        }

        $emailVerifier->sendEmailConfirmation('app_users_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->getParameter('sender_email'), $translator->trans('mails.register_confirmation.name', [], 'mails')))
                ->to($user->getEmail())
                ->subject($translator->trans('mails.register_confirmation.subject', [], 'mails'))
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'name' => $user->getFirstname().' '.$user->getLastname(),
                ])
        );

        return $this->respondNoContent();
    }
}
