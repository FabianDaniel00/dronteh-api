<?php

namespace App\JsonApi\Hydrator\Chemical;

use App\Entity\Chemical;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;

/**
 * Abstract Chemical Hydrator.
 */
abstract class AbstractChemicalHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Chemical::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['chemicals'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($chemical): array
    {
        return [
        ];
    }
}
