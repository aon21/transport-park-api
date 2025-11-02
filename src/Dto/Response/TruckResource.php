<?php

namespace App\Dto\Response;

use App\Entity\Truck;

class TruckResource
{
    public string $id;
    public string $registrationNumber;
    public string $brand;
    public string $model;
    public string $status;
    public string $createdAt;
    public string $updatedAt;

    public static function fromEntity(Truck $truck): self
    {
        $resource = new self();
        $resource->id = $truck->getId()->toRfc4122();
        $resource->registrationNumber = $truck->getRegistrationNumber();
        $resource->brand = $truck->getBrand();
        $resource->model = $truck->getModel();
        $resource->status = $truck->getStatus();
        $resource->createdAt = $truck->getCreatedAt()->format('Y-m-d\TH:i:s\Z');
        $resource->updatedAt = $truck->getUpdatedAt()->format('Y-m-d\TH:i:s\Z');

        return $resource;
    }

    public static function collection(array $trucks): array
    {
        return array_map(fn(Truck $truck) => self::fromEntity($truck), $trucks);
    }
}

