<?php

namespace App\JsonApi\Hydrator\DroneDataPerReservation;

use App\Entity\DroneDataPerReservation;

/**
 * Create DroneDataPerReservation Hydrator.
 */
class CreateDroneDataPerReservationHydrator extends AbstractDroneDataPerReservationHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($droneDataPerReservation): array
    {
        return [
            'gps_coordinates' => function (DroneDataPerReservation $droneDataPerReservation, $attribute, $data, $attributeName) {
                $droneDataPerReservation->setGpsCoordinates($attribute);
            },
            'results' => function (DroneDataPerReservation $droneDataPerReservation, $attribute, $data, $attributeName) {
                $droneDataPerReservation->setResults($attribute);
            },
            'chemical_quantity_per_ha' => function (DroneDataPerReservation $droneDataPerReservation, $attribute, $data, $attributeName) {
                $droneDataPerReservation->setChemicalQuantityPerHa($attribute);
            },
            'water_quantity' => function (DroneDataPerReservation $droneDataPerReservation, $attribute, $data, $attributeName) {
                $droneDataPerReservation->setWaterQuantity($attribute);
            },
            'is_deleted' => function (DroneDataPerReservation $droneDataPerReservation, $attribute, $data, $attributeName) {
                $droneDataPerReservation->setIsDeleted($attribute);
            },
        ];
    }
}