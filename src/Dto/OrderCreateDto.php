<?php

namespace App\Dto;

readonly class OrderCreateDto
{
    public function __construct(
        public string $orderNumber,
        public ?string $truckId,
        public ?string $trailerId,
        public ?string $fleetSetId,
        public string $serviceType,
        public string $description,
        public string $status,
        public string $startDate,
        public ?string $endDate
    ) {
    }
}

