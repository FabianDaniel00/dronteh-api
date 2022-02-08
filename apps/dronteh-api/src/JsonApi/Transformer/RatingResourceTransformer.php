<?php

namespace App\JsonApi\Transformer;

use App\Entity\Rating;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Rating Resource Transformer.
 */
class RatingResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($rating): string
    {
        return 'ratings';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($rating): string
    {
        return (string) $rating->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($rating): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($rating): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/ratings/'.$this->getId($rating)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($rating): array
    {
        return [
            'rating' => function (Rating $rating) {
                return $rating->getRating();
            },
            'is_deleted' => function (Rating $rating) {
                return $rating->getIsDeleted();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($rating): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($rating): array
    {
        return [
            'user' => function (Rating $rating) {
                return ToOneRelationship::create()
                    ->setDataAsCallable(
                        function () use ($rating) {
                            return $rating->getUser();
                        },
                        new UserResourceTransformer()
                    )
                    ->omitDataWhenNotIncluded();
            },
            'chemical' => function (Rating $rating) {
                return ToOneRelationship::create()
                    ->setDataAsCallable(
                        function () use ($rating) {
                            return $rating->getChemical();
                        },
                        new ChemicalResourceTransformer()
                    )
                    ->omitDataWhenNotIncluded();
            },
        ];
    }
}
