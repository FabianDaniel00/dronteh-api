<?php

namespace App\JsonApi\Transformer;

use App\Entity\User;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Link\ResourceLinks;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * User Resource Transformer.
 */
class UserResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($user): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($user): string
    {
        return (string) $user->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($user): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($user): ?ResourceLinks
    {
        return ResourceLinks::createWithoutBaseUri()->setSelf(new Link('/users/'.$this->getId($user)));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($user): array
    {
        return [
            'email' => function (User $user) {
                return $user->getEmail();
            },
            'roles' => function (User $user) {
                return $user->getRoles();
            },
            'password' => function (User $user) {
                return $user->getPassword();
            },
            'firstname' => function (User $user) {
                return $user->getFirstname();
            },
            'lastname' => function (User $user) {
                return $user->getLastname();
            },
            'tel' => function (User $user) {
                return $user->getTel();
            },
            'created_at' => function (User $user) {
                return $user->getCreatedAt()->format(\DATE_ATOM);
            },
            'updated_at' => function (User $user) {
                return $user->getUpdatedAt()->format(\DATE_ATOM);
            },
            'isVerified' => function (User $user) {
                return $user->isVerified();
            },
            'is_deleted' => function (User $user) {
                return $user->isDeleted();
            },
            'locale' => function (User $user) {
                return $user->getLocale();
            },
            'last_verification_email_sent' => function (User $user) {
                $lastVerificationEmailSent = $user->getLastVerificationEmailSent();

                return $lastVerificationEmailSent ? $lastVerificationEmailSent->format(\DATE_ATOM) : null;
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($user): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($user): array
    {
        return [
        ];
    }
}
