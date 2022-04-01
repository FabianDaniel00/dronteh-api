<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class LatLng extends Constraint
{
    public $message = 'validators.reservations.constraint.latlng';
}
