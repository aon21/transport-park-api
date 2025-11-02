<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class TruckCreateRequest
{
    #[Assert\NotBlank(message: 'Registration number is required')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Registration number cannot be longer than {{ limit }} characters'
    )]
    public string $registrationNumber;

    #[Assert\NotBlank(message: 'Brand is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Brand cannot be longer than {{ limit }} characters'
    )]
    public string $brand;

    #[Assert\NotBlank(message: 'Model is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Model cannot be longer than {{ limit }} characters'
    )]
    public string $model;

    #[Assert\NotBlank(message: 'Status is required')]
    #[Assert\Choice(
        choices: ['operational', 'in_service'],
        message: 'Status must be either "operational" or "in_service"'
    )]
    public string $status;
}

