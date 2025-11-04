<?php

namespace App\Dto;

readonly class TrailerCreateDto
{
    public function __construct(
        public string $registrationNumber,
        public string $type,
        public float $capacity,
        public string $status
    ) {
    }
}

