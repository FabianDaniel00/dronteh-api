<?php

namespace App\JsonApi\Hydrator\Plant;

use App\Entity\Plant;

/**
 * Create Plant Hydrator.
 */
class CreatePlantHydrator extends AbstractPlantHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($plant): array
    {
        return [
            'name' => function (Plant $plant, $attribute, $data, $attributeName) {
                $plant->setName($attribute);
            },
        ];
    }
}
