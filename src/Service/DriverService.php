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
        $driver
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName)
            ->setLicenseNumber($dto->licenseNumber);

        // Handle nullable fleetSet assignment (null = unassign driver)
        if ($dto->fleetSetId !== null) {
            $fleetSet = $this->fleetSetRepository->findOrFail($dto->fleetSetId);
            $driver->setFleetSet($fleetSet);
        } else {
            $driver->setFleetSet(null);
        }

        $this->driverRepository->save($driver, true);

        return $driver;
    }

    public function delete(Driver $driver): void
    {
        $this->driverRepository->remove($driver, true);
    }
}

