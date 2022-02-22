<?php

namespace App\JsonApi\Document\DroneDataPerReservation;

use Paknahad\JsonApiBundle\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;

/**
 * DroneDataPerReservation Document.
 */
class DroneDataPerReservationDocument extends AbstractSingleResourceDocument
{
    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?DocumentLinks
    {
        return DocumentLinks::createWithoutBaseUri(
            [
                'self' => new Link('/drone_data_per_reservations/'.$this->getResourceId()),
            ]
        );
    }
}
