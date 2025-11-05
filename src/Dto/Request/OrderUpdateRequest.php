<?php

namespace App\Dto\Request;

use App\Validator\Constraints\AtLeastOneOrderAsset;
use Symfony\Component\Validator\Constraints as Assert;

#[AtLeastOneOrderAsset]
class OrderUpdateRequest
{
    #[Assert\NotBlank(message: 'Order number is required')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Order number cannot be longer than {{ limit }} characters'
    )]
    public string $orderNumber;

    #[Assert\Uuid(message: 'Truck ID must be a valid UUID')]
    public ?string $truckId = null;

    #[Assert\Uuid(message: 'Trailer ID must be a valid UUID')]
    public ?string $trailerId = null;

    #[Assert\Uuid(message: 'Fleet set ID must be a valid UUID')]
    public ?string $fleetSetId = null;

    #[Assert\NotBlank(message: 'Service type is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Service type cannot be longer than {{ limit }} characters'
    )]
    public string $serviceType;

    #[Assert\NotBlank(message: 'Description is required')]
    public string $description;

    #[Assert\NotBlank(message: 'Status is required')]
    #[Assert\Choice(
        choices: ['pending', 'in_progress', 'completed', 'cancelled'],
        message: 'Status must be one of: pending, in_progress, completed, cancelled'
    )]
    public string $status;

    #[Assert\NotBlank(message: 'Start date is required')]
    #[Assert\DateTime(message: 'Start date must be a valid date-time format')]
    public string $startDate;

    #[Assert\DateTime(message: 'End date must be a valid date-time format')]
    public ?string $endDate = null;
}

