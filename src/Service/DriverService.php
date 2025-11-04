<?php

namespace App\Service;

use App\Dto\DriverCreateDto;
use App\Dto\DriverUpdateDto;
use App\Entity\Driver;
use App\Repository\DriverRepository;
use App\Repository\FleetSetRepository;

readonly class DriverService
{
    public function __construct(
        private DriverRepository $driverRepository,
        private FleetSetRepository $fleetSetRepository
    ) {
    }

    public function create(DriverCreateDto $dto): Driver
    {
        $driver = (new Driver())
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName)
            ->setLicenseNumber($dto->licenseNumber);

        if ($dto->fleetSetId !== null) {
            $fleetSet = $this->fleetSetRepository->findOrFail($dto->fleetSetId);
            $driver->setFleetSet($fleetSet);
        }

        $this->driverRepository->save($driver, true);

        return $driver;
    }

    public function update(Driver $driver, DriverUpdateDto $dto): Driver
    {
        $this->updateFirstName($driver, $dto->firstName);
        $this->updateLastName($driver, $dto->lastName);
        $this->updateLicenseNumber($driver, $dto->licenseNumber);
        $this->updateFleetSet($driver, $dto->fleetSetId);

        $this->driverRepository->save($driver, true);

        return $driver;
    }

    private function updateFirstName(Driver $driver, ?string $firstName): void
    {
        if ($firstName !== null) {
            $driver->setFirstName($firstName);
        }
    }

    private function updateLastName(Driver $driver, ?string $lastName): void
    {
        if ($lastName !== null) {
            $driver->setLastName($lastName);
        }
    }

    private function updateLicenseNumber(Driver $driver, ?string $licenseNumber): void
    {
        if ($licenseNumber !== null) {
            $driver->setLicenseNumber($licenseNumber);
        }
    }

    private function updateFleetSet(Driver $driver, ?string $fleetSetId): void
    {
        if ($fleetSetId === null) {
            return;
        }

        $fleetSet = $this->fleetSetRepository->findOrFail($fleetSetId);
        $driver->setFleetSet($fleetSet);
    }

    public function delete(Driver $driver): void
    {
        $this->driverRepository->remove($driver, true);
    }
}

