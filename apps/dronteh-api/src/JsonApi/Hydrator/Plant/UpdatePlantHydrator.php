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
            'name_hu' => function (Plant $plant, $attribute, $data, $attributeName) {
                $plant->setNameHu($attribute);
            },
            'name_en' => function (Plant $plant, $attribute, $data, $attributeName) {
                $plant->setNameEn($attribute);
            },
            'name_sr' => function (Plant $plant, $attribute, $data, $attributeName) {
                $plant->setNameSr($attribute);
            },
            'is_deleted' => function (Plant $plant, $attribute, $data, $attributeName) {
                $plant->setIsDeleted($attribute);
            },
        ];
    }
}