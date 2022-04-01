<?php

namespace App\Validator;

use App\Validator\LatLng;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class LatLngValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof LatLng) {
            throw new UnexpectedTypeException($constraint, LatLng::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_array($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'array');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }

        $latLng = join(';', $value);
        if (!preg_match('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?);\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/', $latLng)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $latLng)
                ->addViolation();
        }
    }
}
