<?php

namespace App\Dto;

readonly class FleetSetUpdateDto
{
    public function __construct(
        public string $name,
        public string $truckId,
        public string $trailerId
    ) {
    }
}

