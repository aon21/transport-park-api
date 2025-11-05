<?php

namespace App\Dto\Response;

use App\Entity\Driver;

class DriverResource
{
    public string  $id;
    public string  $firstName;
    public string  $lastName;
    public string  $fullName;
    public string  $licenseNumber;
    public ?string $fleetSetId;
    public string  $createdAt;
    public string  $updatedAt;

    public static function fromEntity(Driver $driver): self
    {
        $resource = new self();
        $resource->id =            $driver->getId()->toRfc4122();
        $resource->firstName =     $driver->getFirstName();
        $resource->lastName =      $driver->getLastName();
        $resource->fullName =      $driver->getFullName();
        $resource->licenseNumber = $driver->getLicenseNumber();
        $resource->fleetSetId =    $driver->getFleetSet()?->getId()->toRfc4122();
        $resource->createdAt =     $driver->getCreatedAt()->format('Y-m-d\TH:i:s\Z');
        $resource->updatedAt =     $driver->getUpdatedAt()->format('Y-m-d\TH:i:s\Z');

        return $resource;
    }

    public static function collection(array $drivers): array
    {
        return array_map(fn(Driver $driver) => self::fromEntity($driver), $drivers);
    }
}

