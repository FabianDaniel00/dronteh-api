<?php

namespace App\JsonApi\Transformer;

use App\Entity\Contact;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Contact Resource Transformer.
 */
class ContactResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($contact): string
    {
        return 'contacts';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($contact): string
    {
        return (string) $contact->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($contact): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($contact): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/contacts/'.$this->getId($contact)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($contact): array
    {
        return [
            'email' => function (Contact $contact) {
                return $contact->getEmail();
            },
            'message' => function (Contact $contact) {
                return $contact->getMessage();
            },
            'created_at' => function (Contact $contact) {
                return $contact->getCreatedAt()->format(\DATE_ATOM);
            },
            'is_deleted' => function (Contact $contact) {
                return $contact->isDeleted();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($contact): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($contact): array
    {
        return [
        ];
    }
}
