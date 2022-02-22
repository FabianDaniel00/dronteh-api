<?php

namespace App\JsonApi\Document\AreaOfUseChemical;

use Paknahad\JsonApiBundle\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;

/**
 * AreaOfUseChemicals Document.
 */
class AreaOfUseChemicalsDocument extends AbstractCollectionDocument
{
    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?DocumentLinks
    {
        return DocumentLinks::createWithoutBaseUri()
            ->setPagination('/area_of_use_chemicals', $this->object);
    }
}
