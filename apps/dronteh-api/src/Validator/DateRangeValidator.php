<?php

namespace App\Validator;

use App\Validator\DateRange;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class DateRangeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DateRange) {
            throw new UnexpectedTypeException($constraint, DateRange::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof \DateTime) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, '\DateTime');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }

        $reservation = $this->context->getObject()->getRoot()->getData();
        $startDate = new \DateTime($reservation->getReservationIntervalStart()->format('Y-M-d'));
        $endDate = $reservation->getReservationIntervalEnd();
        $currentDateTime = new \DateTime('now');

        if ($startDate < $currentDateTime->modify('-1 day')) {
            $this->context->buildViolation($constraint->messageStart)
                ->setParameter('{{ start_date }}', $startDate->format($constraint->dateFormat))
                ->addViolation()
            ;
        }

        if ($startDate->modify('+'.$constraint->minDays.' days') > $endDate) {
            $this->context->buildViolation($constraint->messageInterval)
                ->setParameter('{{ start_date }}', $startDate->format($constraint->dateFormat))
                ->setParameter('{{ end_date }}', $endDate->format($constraint->dateFormat))
                ->atPath('[reservation_interval_start]')
                ->addViolation()
            ;
        }
    }
}
