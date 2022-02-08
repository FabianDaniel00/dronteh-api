<?php

namespace App\JsonApi\Hydrator\Chemical;

use App\Entity\Chemical;

/**
 * Update Chemical Hydrator.
 */
class UpdateChemicalHydrator extends AbstractChemicalHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($chemical): array
    {
        return [
            'name' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setName($attribute);
            },
            'price_per_liter' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setPricePerLiter($attribute);
            },
            'is_deleted' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setIsDeleted($attribute);
            },
            'area_of_use' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setAreaOfUse($attribute);
            },
        ];
    }
}
