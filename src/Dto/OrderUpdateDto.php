<?php

namespace App\Dto;

readonly class OrderUpdateDto
{
    public function __construct(
        public string $orderNumber,
        public ?string $truckId,        // Nullable in entity - optional assignment
        public ?string $trailerId,      // Nullable in entity - optional assignment
        public ?string $fleetSetId,     // Nullable in entity - optional assignment
        public string $serviceType,
        public string $description,
        public string $status,
        public string $startDate,
        public ?string $endDate         // Nullable in entity - optional completion date
    ) {
    }
}

