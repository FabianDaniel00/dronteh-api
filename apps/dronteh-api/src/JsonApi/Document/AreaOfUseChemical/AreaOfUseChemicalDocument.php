<?php

namespace App\JsonApi\Document\AreaOfUseChemical;

use Paknahad\JsonApiBundle\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\Link\DocumentLinks;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;

/**
 * AreaOfUseChemical Document.
 */
class AreaOfUseChemicalDocument extends AbstractSingleResourceDocument
{
    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?DocumentLinks
    {
        return DocumentLinks::createWithoutBaseUri(
            [
                'self' => new Link('/area_of_use_chemicals/'.$this->getResourceId()),
            ]
        );
    }
}
