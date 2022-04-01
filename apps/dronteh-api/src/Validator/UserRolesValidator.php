<?php

namespace App\Validator;

use App\Validator\UserRoles;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserRolesValidator extends ConstraintValidator
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UserRoles) {
            throw new UnexpectedTypeException($constraint, UserRoles::class);
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

        $invalidRoles = [];
        foreach($value as $role) {
            if (!in_array($role, $this->params->get('app.supported_roles'))) $invalidRoles[] = $role;
        }

        if ($invalidRoles) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', join(', ', $invalidRoles))
                ->setParameter('{{ count }}', count($invalidRoles))
                ->addViolation();
        }
    }
}
