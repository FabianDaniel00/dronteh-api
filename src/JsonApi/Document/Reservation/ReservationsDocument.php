<?php

namespace App\JsonApi\Document\Reservation;

use Paknahad\JsonApiBundle\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;

/**
 * Reservations Document.
 */
class ReservationsDocument extends AbstractCollectionDocument
{
    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?DocumentLinks
    {
        return DocumentLinks::createWithoutBaseUri()
            ->setPagination('/reservations', $this->object);
    }
}
