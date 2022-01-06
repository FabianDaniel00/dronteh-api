<?php

namespace App\JsonApi\Hydrator\User;

use App\Entity\User;

/**
 * Update User Hydrator.
 */
class UpdateUserHydrator extends AbstractUserHydrator
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
            'roles' => function (User $user, $attribute, $data, $attributeName) {
                $user->setRoles($attribute);
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
            'created_at' => function (User $user, $attribute, $data, $attributeName) {
                $user->setCreatedAt(new \DateTime($attribute));
            },
            'updated_at' => function (User $user, $attribute, $data, $attributeName) {
                $user->setUpdatedAt(new \DateTime($attribute));
            },
            'isVerified' => function (User $user, $attribute, $data, $attributeName) {
                $user->setIsVerified($attribute);
            },
            'is_deleted' => function (User $user, $attribute, $data, $attributeName) {
                $user->setIsDeleted($attribute);
            },
        ];
    }
}