<?php

namespace App\JsonApi\Transformer;

use App\Entity\Plant;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Plant Resource Transformer.
 */
class PlantResourceTransformer extends AbstractResource
{
    private $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($plant): string
    {
        return 'plants';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($plant): string
    {
        return (string) $plant->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($plant): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($plant): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/plants/'.$this->getId($plant)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($plant): array
    {
        return [
            'name' => function (Plant $plant) {
                return $plant->{'getName'.ucfirst($this->locale)}();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($plant): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($plant): array
    {
        return [
        ];
    }
}
