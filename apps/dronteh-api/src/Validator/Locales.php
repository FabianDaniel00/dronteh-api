<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Locales extends Constraint
{
    public $message = 'validators.users.constraint.locale';
}
