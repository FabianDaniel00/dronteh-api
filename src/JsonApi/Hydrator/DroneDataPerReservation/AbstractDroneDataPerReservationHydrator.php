<?php

namespace App\JsonApi\Hydrator\DroneDataPerReservation;

use App\Entity\DroneDataPerReservation;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

/**
 * Abstract DroneDataPerReservation Hydrator.
 */
abstract class AbstractDroneDataPerReservationHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return DroneDataPerReservation::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['drone_data_per_reservations'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($droneDataPerReservation): array
    {
        return [
            'reservation' => function (DroneDataPerReservation $droneDataPerReservation, ToOneRelationship $reservation, $data, $relationshipName) {
                $this->validateRelationType($reservation, ['reservations']);


                $association = null;
                $identifier = $reservation->getResourceIdentifier();
                if ($identifier) {
                    $association = $this->objectManager->getRepository('App\Entity\Reservation')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                }

                $droneDataPerReservation->setReservation($association);
            },
        ];
    }
}
