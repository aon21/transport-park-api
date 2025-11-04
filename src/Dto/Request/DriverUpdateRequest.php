<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DriverUpdateRequest
{
    #[Assert\Length(
        max: 100,
        maxMessage: 'First name cannot be longer than {{ limit }} characters'
    )]
    public ?string $firstName;

    #[Assert\Length(
        max: 100,
        maxMessage: 'Last name cannot be longer than {{ limit }} characters'
    )]
    public ?string $lastName;

    #[Assert\Length(
        max: 50,
        maxMessage: 'License number cannot be longer than {{ limit }} characters'
    )]
    public ?string $licenseNumber;

    #[Assert\Uuid(message: 'Fleet set ID must be a valid UUID')]
    public ?string $fleetSetId;
}

