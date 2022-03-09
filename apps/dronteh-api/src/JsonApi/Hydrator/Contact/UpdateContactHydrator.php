<?php

namespace App\JsonApi\Hydrator\Contact;

use App\Entity\Contact;

/**
 * Update Contact Hydrator.
 */
class UpdateContactHydrator extends AbstractContactHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($contact): array
    {
        return [
            'email' => function (Contact $contact, $attribute, $data, $attributeName) {
                $contact->setEmail($attribute);
            },
            'message' => function (Contact $contact, $attribute, $data, $attributeName) {
                $contact->setMessage($attribute);
            },
        ];
    }
}
