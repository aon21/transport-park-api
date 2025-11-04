<?php

namespace App\Dto;

readonly class DriverUpdateDto
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $licenseNumber,
        public ?string $fleetSetId  // Nullable in entity - can be null to unassign
    ) {
    }
}

