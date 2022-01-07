<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AgeConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof AgeConstraint) {
            throw new UnexpectedTypeException($constraint, AgeConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $dateTimeNow = new \DateTime('now');
        $dateTimeBDay = $value;
        $interval = $dateTimeNow->diff($dateTimeBDay);

        if ($interval->y > 150) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ birthdate }}', $value->format('d/m/Y'))
                ->addViolation();
        }
    }
}