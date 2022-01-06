<?php

namespace App\JsonApi\Document\Chemical;

use Paknahad\JsonApiBundle\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;

/**
 * Chemicals Document.
 */
class ChemicalsDocument extends AbstractCollectionDocument
{
    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?DocumentLinks
    {
        return DocumentLinks::createWithoutBaseUri()
            ->setPagination('/chemicals', $this->object);
    }
}
