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
            'created_at' => function (Contact $contact, $attribute, $data, $attributeName) {
                $contact->setCreatedAt(new \DateTime($attribute));
            },
            'is_deleted' => function (Contact $contact, $attribute, $data, $attributeName) {
                $contact->setIsDeleted($attribute);
            },
        ];
    }
}
