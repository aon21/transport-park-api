<?php

namespace App\Dto;

readonly class TrailerUpdateDto
{
    public function __construct(
        public ?string $registrationNumber,
        public ?string $type,
        public ?float $capacity,
        public ?string $status
    ) {
    }
}

