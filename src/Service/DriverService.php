<?php

namespace App\Service;

use App\Entity\Driver;
use App\Repository\DriverRepository;
use App\Repository\FleetSetRepository;

class DriverService
{
    public function __construct(
        private DriverRepository $driverRepository,
        private FleetSetRepository $fleetSetRepository
    ) {
    }

    public function findAll(): array
    {
        return $this->driverRepository->findAll();
    }

    public function findUnassigned(): array
    {
        return $this->driverRepository->findUnassigned();
    }

    public function findById(string $id): ?Driver
    {
        return $this->driverRepository->find($id);
    }

    public function create(array $data): Driver
    {
        $driver = new Driver();
        $driver->setFirstName($data['firstName'])
            ->setLastName($data['lastName'])
            ->setLicenseNumber($data['licenseNumber']);

        if (isset($data['fleetSetId'])) {
            $fleetSet = $this->fleetSetRepository->find($data['fleetSetId']);
            if ($fleetSet) {
                $driver->setFleetSet($fleetSet);
            }
        }

        $this->driverRepository->save($driver, true);

        return $driver;
    }

    public function update(Driver $driver, array $data): Driver
    {
        if (isset($data['firstName'])) {
            $driver->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $driver->setLastName($data['lastName']);
        }
        if (isset($data['licenseNumber'])) {
            $driver->setLicenseNumber($data['licenseNumber']);
        }
        if (isset($data['fleetSetId'])) {
            $fleetSet = $this->fleetSetRepository->find($data['fleetSetId']);
            $driver->setFleetSet($fleetSet);
        }

        $this->driverRepository->save($driver, true);

        return $driver;
    }

    public function delete(Driver $driver): void
    {
        $this->driverRepository->remove($driver, true);
    }
}

