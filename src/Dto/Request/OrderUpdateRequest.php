<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class OrderUpdateRequest
{
    #[Assert\Length(
        max: 50,
        maxMessage: 'Order number cannot be longer than {{ limit }} characters'
    )]
    public ?string $orderNumber;

    #[Assert\Uuid(message: 'Truck ID must be a valid UUID')]
    public ?string $truckId;

    #[Assert\Uuid(message: 'Trailer ID must be a valid UUID')]
    public ?string $trailerId;

    #[Assert\Uuid(message: 'Fleet set ID must be a valid UUID')]
    public ?string $fleetSetId;

    #[Assert\Length(
        max: 100,
        maxMessage: 'Service type cannot be longer than {{ limit }} characters'
    )]
    public ?string $serviceType;

    public ?string $description;

    #[Assert\Choice(
        choices: ['pending', 'in_progress', 'completed', 'cancelled'],
        message: 'Status must be one of: pending, in_progress, completed, cancelled'
    )]
    public ?string $status;

    #[Assert\DateTime(message: 'Start date must be a valid date-time format')]
    public ?string $startDate;

    #[Assert\DateTime(message: 'End date must be a valid date-time format')]
    public ?string $endDate;
}

