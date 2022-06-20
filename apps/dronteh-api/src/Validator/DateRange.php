<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateRange extends Constraint
{
    public $messageStart = 'validators.reservations.constraint.start_date';
    public $messageInterval = 'validators.reservations.constraint.daterange';
    public $minDays = 0;
    public $dateFormat = 'MM/dd/yyyy';
}
