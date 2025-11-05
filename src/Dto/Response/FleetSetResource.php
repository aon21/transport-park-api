<?php

namespace App\Dto\Response;

use App\Entity\FleetSet;

class FleetSetResource
{
    public string $id;
    public string $name;
    public string $truckId;
    public string $trailerId;
    public string $status;
    public int    $driverCount;
    public array  $driverIds;
    public string $createdAt;
    public string $updatedAt;

    public static function fromEntity(FleetSet $fleetSet): self
    {
        $resource = new self();
        $resource->id =          $fleetSet->getId()->toRfc4122();
        $resource->name =        $fleetSet->getName();
        $resource->truckId =     $fleetSet->getTruck()->getId()->toRfc4122();
        $resource->trailerId =   $fleetSet->getTrailer()->getId()->toRfc4122();
        $resource->status =      $fleetSet->getStatus();
        $resource->driverCount = $fleetSet->getDrivers()->count();
        $resource->driverIds =   $fleetSet->getDrivers()
                                    ->map(fn($driver) => $driver->getId()->toRfc4122())
                                    ->toArray();
        $resource->createdAt =   $fleetSet->getCreatedAt()->format('Y-m-d\TH:i:s\Z');
        $resource->updatedAt =   $fleetSet->getUpdatedAt()->format('Y-m-d\TH:i:s\Z');

        return $resource;
    }

    public static function collection(array $fleetSets): array
    {
        return array_map(fn(FleetSet $fleetSet) => self::fromEntity($fleetSet), $fleetSets);
    }
}

