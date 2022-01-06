<?php

namespace App\JsonApi\Transformer;

use App\Entity\Chemical;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Chemical Resource Transformer.
 */
class ChemicalResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($chemical): string
    {
        return 'chemicals';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($chemical): string
    {
        return (string) $chemical->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($chemical): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($chemical): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/chemicals/'.$this->getId($chemical)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($chemical): array
    {
        return [
            'name' => function (Chemical $chemical) {
                return $chemical->getName();
            },
            'price_per_liter' => function (Chemical $chemical) {
                return $chemical->getPricePerLiter();
            },
            'is_deleted' => function (Chemical $chemical) {
                return $chemical->isDeleted();
            },
            'area_of_use' => function (Chemical $chemical) {
                return $chemical->getAreaOfUse();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($chemical): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($chemical): array
    {
        return [
        ];
    }
}