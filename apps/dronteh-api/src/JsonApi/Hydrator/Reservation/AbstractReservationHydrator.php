<?php

namespace App\JsonApi\Hydrator\Reservation;

use App\Entity\Reservation;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

/**
 * Abstract Reservation Hydrator.
 */
abstract class AbstractReservationHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Reservation::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['reservations'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($reservation): array
    {
        return [
            'user' => function (Reservation $reservation, ToOneRelationship $user, $data, $relationshipName) {
                $this->validateRelationType($user, ['users']);


                $association = null;
                $identifier = $user->getResourceIdentifier();
                if ($identifier) {
                    $association = $this->objectManager->getRepository('App\Entity\User')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                }

                $reservation->setUser($association);
            },
            'chemical' => function (Reservation $reservation, ToOneRelationship $chemical, $data, $relationshipName) {
                $this->validateRelationType($chemical, ['chemicals']);


                $association = null;
                $identifier = $chemical->getResourceIdentifier();
                if ($identifier) {
                    $association = $this->objectManager->getRepository('App\Entity\Chemical')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                }

                $reservation->setChemical($association);
            },
            'plant' => function (Reservation $reservation, ToOneRelationship $plant, $data, $relationshipName) {
                $this->validateRelationType($plant, ['plants']);


                $association = null;
                $identifier = $plant->getResourceIdentifier();
                if ($identifier) {
                    $association = $this->objectManager->getRepository('App\Entity\Plant')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                }

                $reservation->setPlant($association);
            },
        ];
    }
}
