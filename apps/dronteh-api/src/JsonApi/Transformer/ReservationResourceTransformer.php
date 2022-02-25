<?php

namespace App\JsonApi\Transformer;

use App\Entity\Reservation;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Reservation Resource Transformer.
 */
class ReservationResourceTransformer extends AbstractResource
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($reservation): string
    {
        return 'reservations';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($reservation): string
    {
        return (string) $reservation->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($reservation): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($reservation): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/reservations/'.$this->getId($reservation)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($reservation): array
    {
        return [
            'parcel_number' => function (Reservation $reservation) {
                return $reservation->getParcelNumber();
            },
            'land_area' => function (Reservation $reservation) {
                return $reservation->getLandArea();
            },
            'created_at' => function (Reservation $reservation) {
                return $reservation->getCreatedAt()->format(\DATE_ATOM);
            },
            'is_deleted' => function (Reservation $reservation) {
                return $reservation->isDeleted();
            },
            'time' => function (Reservation $reservation) {
                $time = $reservation->getTime();

                return $time ? $time->format(\DATE_ATOM) : null;
            },
            'status' => function (Reservation $reservation) {
                return $reservation->getStatus();
            },
            'to_be_present' => function (Reservation $reservation) {
                return $reservation->getToBePresent();
            },
            'comment' => function (Reservation $reservation) {
                return $reservation->getComment();
            },
            'updated_at' => function (Reservation $reservation) {
                return $reservation->getUpdatedAt()->format(\DATE_ATOM);
            },
            'reservation_interval_start' => function (Reservation $reservation) {
                $reservationIntervalStart = $reservation->getReservationIntervalStart();

                return $reservationIntervalStart ? $reservationIntervalStart->format(\DATE_ATOM) : null;
            },
            'reservation_interval_end' => function (Reservation $reservation) {
                $reservationIntervalEnd = $reservation->getReservationIntervalEnd();

                return $reservationIntervalEnd ? $reservationIntervalEnd->format(\DATE_ATOM) : null;
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($reservation): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($reservation): array
    {
        return [
            'user' => function (Reservation $reservation) {
                return ToOneRelationship::create()
                    ->setDataAsCallable(
                        function () use ($reservation) {
                            return $reservation->getUser();
                        },
                        new UserResourceTransformer()
                    )
                    ->omitDataWhenNotIncluded();
            },
            'chemical' => function (Reservation $reservation) {
                return ToOneRelationship::create()
                    ->setDataAsCallable(
                        function () use ($reservation) {
                            return $reservation->getChemical();
                        },
                        new ChemicalResourceTransformer($this->locale)
                    )
                    ->omitDataWhenNotIncluded();
            },
            'plant' => function (Reservation $reservation) {
                return ToOneRelationship::create()
                    ->setDataAsCallable(
                        function () use ($reservation) {
                            return $reservation->getPlant();
                        },
                        new PlantResourceTransformer($this->locale)
                    )
                    ->omitDataWhenNotIncluded();
            },
        ];
    }
}
