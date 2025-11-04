<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class FleetSetUpdateRequest
{
    #[Assert\Length(
        max: 100,
        maxMessage: 'Name cannot be longer than {{ limit }} characters'
    )]
    public ?string $name = null;

    #[Assert\Uuid(message: 'Truck ID must be a valid UUID')]
    public ?string $truckId = null;

    #[Assert\Uuid(message: 'Trailer ID must be a valid UUID')]
    public ?string $trailerId = null;
}

