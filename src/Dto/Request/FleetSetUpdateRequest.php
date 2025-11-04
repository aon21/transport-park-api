<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class FleetSetUpdateRequest
{
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Name cannot be longer than {{ limit }} characters'
    )]
    public string $name;

    #[Assert\NotBlank(message: 'Truck ID is required')]
    #[Assert\Uuid(message: 'Truck ID must be a valid UUID')]
    public string $truckId;

    #[Assert\NotBlank(message: 'Trailer ID is required')]
    #[Assert\Uuid(message: 'Trailer ID must be a valid UUID')]
    public string $trailerId;
}

