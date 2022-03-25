<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserRoles extends Constraint
{
    public $message = 'validators.users.constraint.role';
}
