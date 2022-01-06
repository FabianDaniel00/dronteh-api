<?php

namespace App\JsonApi\Document\Reservation;

use Paknahad\JsonApiBundle\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;

/**
 * Reservation Document.
 */
class ReservationDocument extends AbstractSingleResourceDocument
{
    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?DocumentLinks
    {
        return DocumentLinks::createWithoutBaseUri(
            [
                'self' => new Link('/reservations/'.$this->getResourceId()),
            ]
        );
    }
}
