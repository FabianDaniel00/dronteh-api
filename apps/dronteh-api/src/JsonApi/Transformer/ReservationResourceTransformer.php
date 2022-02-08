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
                return $reservation->getTime()->format(\DATE_ATOM);
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
                        new ChemicalResourceTransformer()
                    )
                    ->omitDataWhenNotIncluded();
            },
            'plant' => function (Reservation $reservation) {
                return ToOneRelationship::create()
                    ->setDataAsCallable(
                        function () use ($reservation) {
                            return $reservation->getPlant();
                        },
                        new PlantResourceTransformer()
                    )
                    ->omitDataWhenNotIncluded();
            },
        ];
    }
}