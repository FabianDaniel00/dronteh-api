<?php

namespace App\JsonApi\Hydrator\Plant;

use App\Entity\Plant;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;

/**
 * Abstract Plant Hydrator.
 */
abstract class AbstractPlantHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Plant::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['plants'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($plant): array
    {
        return [
        ];
    }
}