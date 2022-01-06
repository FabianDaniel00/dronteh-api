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
            'created_at' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setCreatedAt(new \DateTime($attribute));
            },
            'is_deleted' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setIsDeleted($attribute);
            },
            'time' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setTime(new \DateTime($attribute));
            },
            'status' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setStatus($attribute);
            },
            'to_be_present' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setToBePresent($attribute);
            },
            'comment' => function (Reservation $reservation, $attribute, $data, $attributeName) {
                $reservation->setComment($attribute);
            },
        ];
    }
}
