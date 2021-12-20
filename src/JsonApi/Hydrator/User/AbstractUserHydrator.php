<?php

namespace App\JsonApi\Hydrator\User;

use App\Entity\User;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;

/**
 * Abstract User Hydrator.
 */
abstract class AbstractUserHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return User::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['users'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($user): array
    {
        return [
        ];
    }
}
