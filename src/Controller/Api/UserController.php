<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\JsonApi\Document\User\UserDocument;
use App\JsonApi\Document\User\UsersDocument;
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
}