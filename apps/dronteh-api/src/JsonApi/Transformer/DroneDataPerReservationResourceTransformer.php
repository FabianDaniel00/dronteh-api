<?php

namespace App\JsonApi\Transformer;

use App\Entity\DroneDataPerReservation;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * DroneDataPerReservation Resource Transformer.
 */
class DroneDataPerReservationResourceTransformer extends AbstractResource
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($droneDataPerReservation): string
    {
        return 'drone_data_per_reservations';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($droneDataPerReservation): string
    {
        return (string) $droneDataPerReservation->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($droneDataPerReservation): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($droneDataPerReservation): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/drone_data_per_reservations/'.$this->getId($droneDataPerReservation)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($droneDataPerReservation): array
    {
        return [
            'gps_coordinates' => function (DroneDataPerReservation $droneDataPerReservation) {
                return $droneDataPerReservation->getGpsCoordinates();
            },
            'results' => function (DroneDataPerReservation $droneDataPerReservation) {
                return $droneDataPerReservation->getResults();
            },
            'chemical_quantity_per_ha' => function (DroneDataPerReservation $droneDataPerReservation) {
                return $droneDataPerReservation->getChemicalQuantityPerHa();
            },
            'water_quantity' => function (DroneDataPerReservation $droneDataPerReservation) {
                return $droneDataPerReservation->getWaterQuantity();
            },
            'is_deleted' => function (DroneDataPerReservation $droneDataPerReservation) {
                return $droneDataPerReservation->isDeleted();
            },
            'created_at' => function (DroneDataPerReservation $droneDataPerReservation) {
                return $droneDataPerReservation->getCreatedAt()->format(\DATE_ATOM);
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($droneDataPerReservation): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($droneDataPerReservation): array
    {
        return [
            'reservation' => function (DroneDataPerReservation $droneDataPerReservation) {
                return ToOneRelationship::create()
                    ->setDataAsCallable(
                        function () use ($droneDataPerReservation) {
                            return $droneDataPerReservation->getReservation();
                        },
                        new ReservationResourceTransformer($this->locale)
                    )
                    ->omitDataWhenNotIncluded();
            },
        ];
    }
}
