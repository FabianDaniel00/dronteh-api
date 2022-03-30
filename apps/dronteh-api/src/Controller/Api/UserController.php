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
use Symfony\Component\HttpKernel\KernelInterface;
// use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use App\JsonApi\Transformer\UserResourceTransformer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

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
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher, EmailVerifier $emailVerifier, TranslatorInterface $translator, KernelInterface $kernel): Response
    {
        if ($kernel->getEnvironment() !== 'dev' && !$this->isCsrfTokenValid('user-register', $request->headers->get('x-csrf-token'))) {
            // throw new AccessDeniedHttpException($translator->trans('api.users.new.invlaid_csrf_token', [], 'api'));
            throw new InvalidCsrfTokenException();
        }

        $user = $this->jsonApi()->hydrate(
            new CreateUserHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new User($request->headers->get('x-captcha'), $this->getParameter('app.supported_locales'))
        );

        $this->validate($user);

        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $this->sendVerificationEmail($emailVerifier, $user, $translator, $user->getLocale());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

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
    public function edit(User $user, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException($translator->trans('api.current_user.null', [], 'api'));
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
    public function delete(User $user, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException($translator->trans('api.current_user.null', [], 'api'));
        }

        if ($user->isDeleted() || $user->getId() !== $currentUser->getId()) {
            throw $this->createNotFoundException($translator->trans('api.users.not_found', [], 'api'));
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

        if ($user === null || $user->isDeleted()) {
            throw $this->createNotFoundException($translator->trans('api.users.not_found', [], 'api'));
        }

        if ($user->isVerified()) {
            throw new AccessDeniedHttpException($translator->trans('api.users.is_verified', [], 'api'));
        }

        $threshhold = 10; //minutes

        $now = new \DateTime('@'.strtotime('now'));
        $lastVerificationEmailSent = $user->getLastVerificationEmailSent() ? strtotime($user->getLastVerificationEmailSent()->format('Y-m-d H:i:s')) : strtotime($now->modify('+'.$threshhold.' minutes')->format('Y-m-d H:i:s'));
        $difference = round(abs(strtotime($now->format('Y-m-d H:i:s')) - $lastVerificationEmailSent) / 60);
        if ($difference < $threshhold) {
            throw new AccessDeniedHttpException($translator->trans('api.users.ask_another_user_verification_email.threshold', ['%difference' => $threshhold - $difference], 'api'));
        }

        $this->sendVerificationEmail($emailVerifier, $user, $translator, $user->getLocale());

        $user->setLastVerificationEmailSent(new \DateTime('@'.strtotime('now')));
        $this->entityManager->flush();

        return $this->respondNoContent();
    }

    private function sendVerificationEmail(EmailVerifier $emailVerifier, User $user, TranslatorInterface $translator, string $locale): void {
        $emailVerifier->sendEmailConfirmation('app_users_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address($this->getParameter('sender_email'), $translator->trans('mails.register_confirmation.name', [], 'mails', $locale)))
                ->to($user->getEmail())
                ->subject($translator->trans('mails.register_confirmation.subject', [], 'mails', $locale))
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'name' => $user->getFirstname().' '.$user->getLastname(),
                    'locale' => $locale,
                ])
        );
    }
}
