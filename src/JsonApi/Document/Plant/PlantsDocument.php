<?php

namespace App\JsonApi\Document\Plant;

use Paknahad\JsonApiBundle\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;

/**
 * Plants Document.
 */
class PlantsDocument extends AbstractCollectionDocument
{
    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?DocumentLinks
    {
        return DocumentLinks::createWithoutBaseUri()
            ->setPagination('/plants', $this->object);
    }
}
