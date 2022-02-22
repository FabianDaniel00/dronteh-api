<?php

namespace App\JsonApi\Hydrator\Contact;

use App\Entity\Contact;

/**
 * Create Contact Hydrator.
 */
class CreateContactHydrator extends AbstractContactHydrator
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
