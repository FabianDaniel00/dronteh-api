<?php

namespace App\JsonApi\Hydrator\User;

use App\Entity\User;

/**
 * Create User Hydrator.
 */
class CreateUserHydrator extends AbstractUserHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($user): array
    {
        return [
            'email' => function (User $user, $attribute, $data, $attributeName) {
                $user->setEmail($attribute);
            },
            'password' => function (User $user, $attribute, $data, $attributeName) {
                $user->setPassword($attribute);
            },
            'firstname' => function (User $user, $attribute, $data, $attributeName) {
                $user->setFirstname($attribute);
            },
            'lastname' => function (User $user, $attribute, $data, $attributeName) {
                $user->setLastname($attribute);
            },
            'tel' => function (User $user, $attribute, $data, $attributeName) {
                $user->setTel($attribute);
            },
            'locale' => function (User $user, $attribute, $data, $attributeName) {
                $user->setLocale($attribute);
            },
        ];
    }
}
