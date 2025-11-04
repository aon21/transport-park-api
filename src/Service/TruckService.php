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
        $this->updateRegistrationNumber($truck, $dto->registrationNumber);
        $this->updateBrand($truck, $dto->brand);
        $this->updateModel($truck, $dto->model);
        $this->updateStatus($truck, $dto->status);

        $this->truckRepository->save($truck, true);

        return $truck;
    }

    private function updateRegistrationNumber(Truck $truck, ?string $registrationNumber): void
    {
        if ($registrationNumber !== null) {
            $truck->setRegistrationNumber($registrationNumber);
        }
    }

    private function updateBrand(Truck $truck, ?string $brand): void
    {
        if ($brand !== null) {
            $truck->setBrand($brand);
        }
    }

    private function updateModel(Truck $truck, ?string $model): void
    {
        if ($model !== null) {
            $truck->setModel($model);
        }
    }

    private function updateStatus(Truck $truck, ?string $status): void
    {
        if ($status !== null) {
            $truck->setStatus($status);
        }
    }

    public function delete(Truck $truck): void
    {
        $this->truckRepository->remove($truck, true);
    }
}

