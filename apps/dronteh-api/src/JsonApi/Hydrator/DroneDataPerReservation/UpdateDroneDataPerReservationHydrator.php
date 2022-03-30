<?php

namespace App\JsonApi\Hydrator\DroneDataPerReservation;

use App\Entity\DroneDataPerReservation;

/**
 * Update DroneDataPerReservation Hydrator.
 */
class UpdateDroneDataPerReservationHydrator extends AbstractDroneDataPerReservationHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($droneDataPerReservation): array
    {
        return [
            'results' => function (DroneDataPerReservation $droneDataPerReservation, $attribute, $data, $attributeName) {
                $droneDataPerReservation->setResults($attribute);
            },
            'chemical_quantity_per_ha' => function (DroneDataPerReservation $droneDataPerReservation, $attribute, $data, $attributeName) {
                $droneDataPerReservation->setChemicalQuantityPerHa($attribute);
            },
            'water_quantity' => function (DroneDataPerReservation $droneDataPerReservation, $attribute, $data, $attributeName) {
                $droneDataPerReservation->setWaterQuantity($attribute);
            },
        ];
    }
}
