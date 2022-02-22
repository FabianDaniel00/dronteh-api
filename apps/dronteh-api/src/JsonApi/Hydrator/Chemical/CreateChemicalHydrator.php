<?php

namespace App\JsonApi\Hydrator\Chemical;

use App\Entity\Chemical;

/**
 * Create Chemical Hydrator.
 */
class CreateChemicalHydrator extends AbstractChemicalHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($chemical): array
    {
        return [
            'price_per_liter' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setPricePerLiter($attribute);
            },
            'is_deleted' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setIsDeleted($attribute);
            },
            'name_hu' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setNameHu($attribute);
            },
            'name_en' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setNameEn($attribute);
            },
            'name_sr' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setNameSr($attribute);
            },
        ];
    }
}
