<?php

namespace App\JsonApi\Document\DroneDataPerReservation;

use Paknahad\JsonApiBundle\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;

/**
 * DroneDataPerReservations Document.
 */
class DroneDataPerReservationsDocument extends AbstractCollectionDocument
{
    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?DocumentLinks
    {
        return DocumentLinks::createWithoutBaseUri()
            ->setPagination('/drone_data_per_reservations', $this->object);
    }
}
