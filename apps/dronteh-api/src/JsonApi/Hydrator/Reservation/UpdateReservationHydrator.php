<?php

namespace App\JsonApi\Hydrator\Reservation;

use App\Entity\Reservation;

/**
 * Update Reservation Hydrator.
 */
class UpdateReservationHydrator extends AbstractReservationHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($reservation): array
    {
        return [
            'parcel_number' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setParcelNumber($attribute);
            },
            'land_area' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setLandArea($attribute);
            },
            'to_be_present' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setToBePresent($attribute);
            },
            'comment' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setComment($attribute);
            },
            'reservation_interval_start' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setReservationIntervalStart($attribute);
            },
            'reservation_interval_end' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setReservationIntervalEnd($attribute);
            },
        ];
    }
}
