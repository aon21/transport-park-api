<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class OrderCreateRequest
{
    #[Assert\NotBlank(message: 'Order number is required')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Order number cannot be longer than {{ limit }} characters'
    )]
    public string $orderNumber;

    #[Assert\Uuid(message: 'Truck ID must be a valid UUID')]
    public ?string $truckId;

    #[Assert\Uuid(message: 'Trailer ID must be a valid UUID')]
    public ?string $trailerId;

    #[Assert\Uuid(message: 'Fleet set ID must be a valid UUID')]
    public ?string $fleetSetId;

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
    public ?string $endDate;

    #[Assert\Expression(
        "this.truckId !== null || this.trailerId !== null || this.fleetSetId !== null",
        message: 'At least one of truck, trailer, or fleet set must be specified'
    )]
    public function isValid(): bool
    {
        return true;
    }
}

