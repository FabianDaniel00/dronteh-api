<?php

namespace App\JsonApi\Hydrator\Rating;

use App\Entity\Rating;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

/**
 * Abstract Rating Hydrator.
 */
abstract class AbstractRatingHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return Rating::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['ratings'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($rating): array
    {
        return [
            'user' => function (Rating $rating, ToOneRelationship $user, $data, $relationshipName) {
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

                $rating->setUser($association);
            },
            'chemical' => function (Rating $rating, ToOneRelationship $chemical, $data, $relationshipName) {
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

                $rating->setChemical($association);
            },
        ];
    }
}
