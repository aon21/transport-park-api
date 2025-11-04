<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DriverCreateRequest
{
    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'First name cannot be longer than {{ limit }} characters'
    )]
    public string $firstName;

    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Last name cannot be longer than {{ limit }} characters'
    )]
    public string $lastName;

    #[Assert\NotBlank(message: 'License number is required')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'License number cannot be longer than {{ limit }} characters'
    )]
    public string $licenseNumber;

    #[Assert\Uuid(message: 'Fleet set ID must be a valid UUID')]
    public ?string $fleetSetId;
}

