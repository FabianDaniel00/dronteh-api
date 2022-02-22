<?php

namespace App\JsonApi\Hydrator\AreaOfUseChemical;

use App\Entity\AreaOfUseChemical;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

/**
 * Abstract AreaOfUseChemical Hydrator.
 */
abstract class AbstractAreaOfUseChemicalHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getClass(): string
    {
        return AreaOfUseChemical::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['area_of_use_chemicals'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($areaOfUseChemical): array
    {
        return [
            'plant' => function (AreaOfUseChemical $areaOfUseChemical, ToOneRelationship $plant, $data, $relationshipName) {
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

                $areaOfUseChemical->setPlant($association);
            },
            'chemical' => function (AreaOfUseChemical $areaOfUseChemical, ToOneRelationship $chemical, $data, $relationshipName) {
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

                $areaOfUseChemical->setChemical($association);
            },
        ];
    }
}
