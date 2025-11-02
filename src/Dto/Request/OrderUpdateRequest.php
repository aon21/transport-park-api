<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class OrderUpdateRequest
{
    #[Assert\Length(
        max: 50,
        maxMessage: 'Order number cannot be longer than {{ limit }} characters'
    )]
    public ?string $orderNumber = null;

    #[Assert\Uuid(message: 'Truck ID must be a valid UUID')]
    public ?string $truckId = null;

    #[Assert\Uuid(message: 'Trailer ID must be a valid UUID')]
    public ?string $trailerId = null;

    #[Assert\Uuid(message: 'Fleet set ID must be a valid UUID')]
    public ?string $fleetSetId = null;

    #[Assert\Length(
        max: 100,
        maxMessage: 'Service type cannot be longer than {{ limit }} characters'
    )]
    public ?string $serviceType = null;

    public ?string $description = null;

    #[Assert\Choice(
        choices: ['pending', 'in_progress', 'completed', 'cancelled'],
        message: 'Status must be one of: pending, in_progress, completed, cancelled'
    )]
    public ?string $status = null;

    #[Assert\DateTime(message: 'Start date must be a valid date-time format')]
    public ?string $startDate = null;

    #[Assert\DateTime(message: 'End date must be a valid date-time format')]
    public ?string $endDate = null;
}

