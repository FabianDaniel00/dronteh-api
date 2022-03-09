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
            'price_per_liter' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setPricePerLiter($attribute);
            },
            'name_hu' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setNameHu($attribute);
            },
            'name_en' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setNameEn($attribute);
            },
            'name_sr_Latn' => function (Chemical $chemical, $attribute, $data, $attributeName) {
                $chemical->setNameSr_Latn($attribute);
            },
        ];
    }
}
