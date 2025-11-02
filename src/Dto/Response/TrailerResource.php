<?php

namespace App\Dto\Response;

use App\Entity\Trailer;

class TrailerResource
{
    public string $id;
    public string $registrationNumber;
    public string $type;
    public float $capacity;
    public string $status;
    public string $createdAt;
    public string $updatedAt;

    public static function fromEntity(Trailer $trailer): self
    {
        $resource = new self();
        $resource->id = $trailer->getId()->toRfc4122();
        $resource->registrationNumber = $trailer->getRegistrationNumber();
        $resource->type = $trailer->getType();
        $resource->capacity = (float) $trailer->getCapacity();
        $resource->status = $trailer->getStatus();
        $resource->createdAt = $trailer->getCreatedAt()->format('Y-m-d\TH:i:s\Z');
        $resource->updatedAt = $trailer->getUpdatedAt()->format('Y-m-d\TH:i:s\Z');

        return $resource;
    }

    public static function collection(array $trailers): array
    {
        return array_map(fn(Trailer $trailer) => self::fromEntity($trailer), $trailers);
    }
}

