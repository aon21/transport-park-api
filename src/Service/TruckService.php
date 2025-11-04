<?php

namespace App\Service;

use App\Dto\TruckCreateDto;
use App\Dto\TruckUpdateDto;
use App\Entity\Truck;
use App\Repository\TruckRepository;

readonly class TruckService
{
    public function __construct(
        private TruckRepository $truckRepository
    ) {
    }

    public function create(TruckCreateDto $dto): Truck
    {
        $truck = (new Truck())
            ->setRegistrationNumber($dto->registrationNumber)
            ->setBrand($dto->brand)
            ->setModel($dto->model)
            ->setStatus($dto->status);

        $this->truckRepository->save($truck, true);

        return $truck;
    }

    public function update(Truck $truck, TruckUpdateDto $dto): Truck
    {
        $truck
            ->setRegistrationNumber($dto->registrationNumber)
            ->setBrand($dto->brand)
            ->setModel($dto->model)
            ->setStatus($dto->status);

        $this->truckRepository->save($truck, true);

        return $truck;
    }

    public function delete(Truck $truck): void
    {
        $this->truckRepository->remove($truck, true);
    }
}

