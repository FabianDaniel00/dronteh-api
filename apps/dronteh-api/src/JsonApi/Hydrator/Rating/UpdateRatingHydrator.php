<?php

namespace App\JsonApi\Hydrator\Rating;

use App\Entity\Rating;

/**
 * Update Rating Hydrator.
 */
class UpdateRatingHydrator extends AbstractRatingHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($rating): array
    {
        return [
            'rating' => function (Rating $rating, $attribute, $data, $attributeName) {
                $rating->setRating($attribute);
            },
            'is_deleted' => function (Rating $rating, $attribute, $data, $attributeName) {
                $rating->setIsDeleted($attribute);
            },
        ];
    }
}