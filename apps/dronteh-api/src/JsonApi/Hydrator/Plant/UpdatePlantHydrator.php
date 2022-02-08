<?php

namespace App\JsonApi\Hydrator\Plant;

use App\Entity\Plant;

/**
 * Update Plant Hydrator.
 */
class UpdatePlantHydrator extends AbstractPlantHydrator
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
