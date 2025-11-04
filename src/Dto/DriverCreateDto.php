<?php

namespace App\Dto;

readonly class DriverCreateDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $licenseNumber,
        public ?string $fleetSetId
    ) {
    }
}

