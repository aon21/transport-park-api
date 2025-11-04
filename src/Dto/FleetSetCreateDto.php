<?php

namespace App\Dto;

readonly class FleetSetCreateDto
{
    public function __construct(
        public string $name,
        public string $truckId,
        public string $trailerId
    ) {
    }
}

