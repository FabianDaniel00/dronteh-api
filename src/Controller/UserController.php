<?php

namespace App\Controller;

use App\Entity\User;
use App\JsonApi\Document\User\UserDocument;
use App\JsonApi\Document\User\UsersDocument;
use App\JsonApi\Hydrator\User\CreateUserHydrator;
use App\JsonApi\Hydrator\User\UpdateUserHydrator;
use App\JsonApi\Transformer\UserResourceTransformer;
use App\Repository\UserRepository;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="users_index", methods="GET")
     */
    public function index(UserRepository $userRepository, ResourceCollection $resourceCollection): Response
    {
        $resourceCollection->setRepository($userRepository);

        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new UsersDocument(new UserResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="users_new", methods="POST")
     */
    public function new(): Response
    {
        $user = $this->jsonApi()->hydrate(
            new CreateUserHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new User()
        );

        $this->validate($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->respondOk(
            new UserDocument(new UserResourceTransformer()),
            $user
        );
    }

    /**
     * @Route("/{id}", name="users_show", methods="GET")
     */
    public function show(User $user): Response
    {
        return $this->respondOk(
            new UserDocument(new UserResourceTransformer()),
            $user
        );
    }

    /**
     * @Route("/{id}", name="users_edit", methods="PATCH")
     */
    public function edit(User $user): Response
    {
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
     * @Route("/{id}", name="users_delete", methods="DELETE")
     */
    public function delete(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }
}
