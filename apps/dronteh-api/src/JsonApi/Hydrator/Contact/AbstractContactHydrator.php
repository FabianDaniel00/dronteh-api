<?php

namespace App\JsonApi\Hydrator\Contact;

use App\Entity\Contact;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;

/**
 * Abstract Contact Hydrator.
 */
abstract class AbstractContactHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Contact::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['contacts'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($contact): array
    {
        return [
        ];
    }
}
