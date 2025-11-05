<?php

namespace App\Dto\Response;

use App\Entity\Order;

class OrderResource
{
    public string  $id;
    public string  $orderNumber;
    public ?string $truckId;
    public ?string $trailerId;
    public ?string $fleetSetId;
    public string  $serviceType;
    public string  $description;
    public string  $status;
    public bool    $isActive;
    public string  $startDate;
    public ?string $endDate;
    public string  $createdAt;
    public string  $updatedAt;

    public static function fromEntity(Order $order): self
    {
        $resource = new self();
        $resource->id =          $order->getId()->toRfc4122();
        $resource->orderNumber = $order->getOrderNumber();
        $resource->truckId =     $order->getTruck()?->getId()->toRfc4122();
        $resource->trailerId =   $order->getTrailer()?->getId()->toRfc4122();
        $resource->fleetSetId =  $order->getFleetSet()?->getId()->toRfc4122();
        $resource->serviceType = $order->getServiceType();
        $resource->description = $order->getDescription();
        $resource->status =      $order->getStatus();
        $resource->isActive =    $order->isActive();
        $resource->startDate =   $order->getStartDate()->format('Y-m-d\TH:i:s\Z');
        $resource->endDate =     $order->getEndDate()?->format('Y-m-d\TH:i:s\Z');
        $resource->createdAt =   $order->getCreatedAt()->format('Y-m-d\TH:i:s\Z');
        $resource->updatedAt =   $order->getUpdatedAt()->format('Y-m-d\TH:i:s\Z');

        return $resource;
    }

    public static function collection(array $orders): array
    {
        return array_map(fn(Order $order) => self::fromEntity($order), $orders);
    }
}

