<?php

namespace App\JsonApi\Transformer;

use App\Entity\AreaOfUseChemical;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * AreaOfUseChemical Resource Transformer.
 */
class AreaOfUseChemicalResourceTransformer extends AbstractResource
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($areaOfUseChemical): string
    {
        return 'area_of_use_chemicals';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($areaOfUseChemical): string
    {
        return (string) $areaOfUseChemical->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($areaOfUseChemical): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($areaOfUseChemical): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/area_of_use_chemicals/'.$this->getId($areaOfUseChemical)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($areaOfUseChemical): array
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($areaOfUseChemical): array
    {
        return ['plant'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($areaOfUseChemical): array
    {
        return [
            'plant' => function (AreaOfUseChemical $areaOfUseChemical) {
                return ToOneRelationship::create()
                    ->setDataAsCallable(
                        function () use ($areaOfUseChemical) {
                            return $areaOfUseChemical->getPlant();
                        },
                        new PlantResourceTransformer($this->locale)
                    )
                    ->omitDataWhenNotIncluded();
            },
            'chemical' => function (AreaOfUseChemical $areaOfUseChemical) {
                return ToOneRelationship::create()
                    ->setDataAsCallable(
                        function () use ($areaOfUseChemical) {
                            return $areaOfUseChemical->getChemical();
                        },
                        new ChemicalResourceTransformer($this->locale)
                    )
                    ->omitDataWhenNotIncluded();
            },
        ];
    }
}
