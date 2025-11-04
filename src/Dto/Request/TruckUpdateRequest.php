<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class TruckUpdateRequest
{
    #[Assert\Length(
        max: 50,
        maxMessage: 'Registration number cannot be longer than {{ limit }} characters'
    )]
    public ?string $registrationNumber;

    #[Assert\Length(
        max: 100,
        maxMessage: 'Brand cannot be longer than {{ limit }} characters'
    )]
    public ?string $brand;

    #[Assert\Length(
        max: 100,
        maxMessage: 'Model cannot be longer than {{ limit }} characters'
    )]
    public ?string $model;

    #[Assert\Choice(
        choices: ['operational', 'in_service'],
        message: 'Status must be either "operational" or "in_service"'
    )]
    public ?string $status;
}

