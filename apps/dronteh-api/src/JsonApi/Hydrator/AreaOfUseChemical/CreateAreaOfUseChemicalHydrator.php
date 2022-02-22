<?php

namespace App\JsonApi\Hydrator\AreaOfUseChemical;

use App\Entity\AreaOfUseChemical;

/**
 * Create AreaOfUseChemical Hydrator.
 */
class CreateAreaOfUseChemicalHydrator extends AbstractAreaOfUseChemicalHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($areaOfUseChemical): array
    {
        return [
        ];
    }
}
