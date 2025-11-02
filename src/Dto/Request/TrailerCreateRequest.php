<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class TrailerCreateRequest
{
    #[Assert\NotBlank(message: 'Registration number is required')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Registration number cannot be longer than {{ limit }} characters'
    )]
    public string $registrationNumber;

    #[Assert\NotBlank(message: 'Type is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Type cannot be longer than {{ limit }} characters'
    )]
    public string $type;

    #[Assert\NotBlank(message: 'Capacity is required')]
    #[Assert\Positive(message: 'Capacity must be a positive number')]
    #[Assert\LessThanOrEqual(
        value: 999999.99,
        message: 'Capacity cannot exceed {{ compared_value }}'
    )]
    public float $capacity;

    #[Assert\NotBlank(message: 'Status is required')]
    #[Assert\Choice(
        choices: ['operational', 'in_service'],
        message: 'Status must be either "operational" or "in_service"'
    )]
    public string $status;
}

