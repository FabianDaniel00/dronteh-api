<?php

namespace App\JsonApi\Hydrator\Rating;

use App\Entity\Rating;

/**
 * Create Rating Hydrator.
 */
class CreateRatingHydrator extends AbstractRatingHydrator
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
        ];
    }
}
