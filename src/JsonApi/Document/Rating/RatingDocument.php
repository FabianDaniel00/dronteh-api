<?php

namespace App\JsonApi\Document\Rating;

use Paknahad\JsonApiBundle\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;

/**
 * Rating Document.
 */
class RatingDocument extends AbstractSingleResourceDocument
{
    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?DocumentLinks
    {
        return DocumentLinks::createWithoutBaseUri(
            [
                'self' => new Link('/ratings/'.$this->getResourceId()),
            ]
        );
    }
}
