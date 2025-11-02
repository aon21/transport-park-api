<?php

namespace App\Service;

use App\Entity\Truck;
use App\Repository\TruckRepository;

class TruckService
{
    public function __construct(
        private TruckRepository $truckRepository
    ) {
    }

    public function findAll(?string $status = null): array
    {
        return $status
            ? $this->truckRepository->findByStatus($status)
            : $this->truckRepository->findAll();
    }

    public function findById(string $id): ?Truck
    {
        return $this->truckRepository->find($id);
    }

    public function create(array $data): Truck
    {
        $truck = new Truck();
        $truck->setRegistrationNumber($data['registrationNumber'])
            ->setBrand($data['brand'])
            ->setModel($data['model'])
            ->setStatus($data['status']);

        $this->truckRepository->save($truck, true);

        return $truck;
    }

    public function update(Truck $truck, array $data): Truck
    {
        if (isset($data['registrationNumber'])) {
            $truck->setRegistrationNumber($data['registrationNumber']);
        }

        if (isset($data['brand'])) {
            $truck->setBrand($data['brand']);
        }

        if (isset($data['model'])) {
            $truck->setModel($data['model']);
        }

        if (isset($data['status'])) {
            $truck->setStatus($data['status']);
        }

        $this->truckRepository->save($truck, true);

        return $truck;
    }

    public function delete(Truck $truck): void
    {
        $this->truckRepository->remove($truck, true);
    }
}

