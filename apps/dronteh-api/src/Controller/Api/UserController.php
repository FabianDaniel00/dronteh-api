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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

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
    public function new(UserPasswordHasherInterface $userPasswordHasher, EmailVerifier $emailVerifier): Response
    {
        $user = $this->jsonApi()->hydrate(
            new CreateUserHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new User()
        );

        $this->validate($user);

        $user->setCaptcha(null);
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
                ->from(new Address($this->getParameter('sender_email'), 'DronTeh Confirm'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
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
            throw $this->createNotFoundException("Can't find this user");
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
            throw $this->createNotFoundException("Can't find this user");
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
            throw $this->createNotFoundException("Can't find this user");
        }

        $user->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }

    /**
     * @Route("/verify/email", name="api_users_verify_email", methods="GET")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EmailVerifier $emailVerifier): Response
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
            $emailVerifier->handleEmailConfirmation($request, $user);
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