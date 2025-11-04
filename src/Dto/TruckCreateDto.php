<?php

namespace App\Dto;

readonly class TruckCreateDto
{
    public function __construct(
        public string $registrationNumber,
        public string $brand,
        public string $model,
        public string $status
    ) {
    }
}

