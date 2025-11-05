<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AtLeastOneOrderAsset extends Constraint
{
    public string $message = 'Order must have at least one asset assigned (truck, trailer, or fleet set)';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

