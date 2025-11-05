<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AtLeastOneOrderAssetValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AtLeastOneOrderAsset) {
            throw new UnexpectedTypeException($constraint, AtLeastOneOrderAsset::class);
        }

        if ($value === null) {
            return;
        }

        $hasAsset = !empty($value->truckId)
            || !empty($value->trailerId)
            || !empty($value->fleetSetId);

        if (!$hasAsset) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}

