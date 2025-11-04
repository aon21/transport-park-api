<?php

namespace App\Service;

use App\Dto\OrderCreateDto;
use App\Dto\OrderUpdateDto;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\TruckRepository;
use App\Repository\TrailerRepository;
use App\Repository\FleetSetRepository;
use DateTime;
use Exception;

readonly class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private TruckRepository $truckRepository,
        private TrailerRepository $trailerRepository,
        private FleetSetRepository $fleetSetRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function create(OrderCreateDto $dto): Order
    {
        $order = (new Order())
            ->setOrderNumber($dto->orderNumber)
            ->setServiceType($dto->serviceType)
            ->setDescription($dto->description)
            ->setStatus($dto->status)
            ->setStartDate(new DateTime($dto->startDate));

        $this->setOptionalEndDate($order, $dto->endDate);
        $this->setOptionalTruck($order, $dto->truckId);
        $this->setOptionalTrailer($order, $dto->trailerId);
        $this->setOptionalFleetSet($order, $dto->fleetSetId);

        $this->orderRepository->save($order, true);

        return $order;
    }

    /**
     * @throws Exception
     */
    public function update(Order $order, OrderUpdateDto $dto): Order
    {
        $this->updateOrderNumber($order, $dto->orderNumber);
        $this->updateServiceType($order, $dto->serviceType);
        $this->updateDescription($order, $dto->description);
        $this->updateStatus($order, $dto->status);
        $this->updateStartDate($order, $dto->startDate);
        $this->updateEndDate($order, $dto->endDate);
        $this->updateTruck($order, $dto->truckId);
        $this->updateTrailer($order, $dto->trailerId);
        $this->updateFleetSet($order, $dto->fleetSetId);

        $this->orderRepository->save($order, true);

        return $order;
    }

    private function updateOrderNumber(Order $order, ?string $orderNumber): void
    {
        if ($orderNumber !== null) {
            $order->setOrderNumber($orderNumber);
        }
    }

    private function updateServiceType(Order $order, ?string $serviceType): void
    {
        if ($serviceType !== null) {
            $order->setServiceType($serviceType);
        }
    }

    private function updateDescription(Order $order, ?string $description): void
    {
        if ($description !== null) {
            $order->setDescription($description);
        }
    }

    private function updateStatus(Order $order, ?string $status): void
    {
        if ($status !== null) {
            $order->setStatus($status);
        }
    }

    /**
     * @throws Exception
     */
    private function updateStartDate(Order $order, ?string $startDate): void
    {
        if ($startDate !== null) {
            $order->setStartDate(new DateTime($startDate));
        }
    }

    /**
     * @throws Exception
     */
    private function updateEndDate(Order $order, ?string $endDate): void
    {
        if ($endDate !== null) {
            $order->setEndDate(new DateTime($endDate));
        }
    }

    private function updateTruck(Order $order, ?string $truckId): void
    {
        if ($truckId === null) {
            return;
        }

        $order->setTruck($this->truckRepository->findOrFail($truckId));
    }

    private function updateTrailer(Order $order, ?string $trailerId): void
    {
        if ($trailerId === null) {
            return;
        }

        $order->setTrailer($this->trailerRepository->findOrFail($trailerId));
    }

    private function updateFleetSet(Order $order, ?string $fleetSetId): void
    {
        if ($fleetSetId === null) {
            return;
        }

        $order->setFleetSet($this->fleetSetRepository->findOrFail($fleetSetId));
    }

    /**
     * @throws Exception
     */
    private function setOptionalEndDate(Order $order, ?string $endDate): void
    {
        if ($endDate !== null) {
            $order->setEndDate(new DateTime($endDate));
        }
    }

    private function setOptionalTruck(Order $order, ?string $truckId): void
    {
        if ($truckId !== null) {
            $order->setTruck($this->truckRepository->findOrFail($truckId));
        }
    }

    private function setOptionalTrailer(Order $order, ?string $trailerId): void
    {
        if ($trailerId !== null) {
            $order->setTrailer($this->trailerRepository->findOrFail($trailerId));
        }
    }

    private function setOptionalFleetSet(Order $order, ?string $fleetSetId): void
    {
        if ($fleetSetId !== null) {
            $order->setFleetSet($this->fleetSetRepository->findOrFail($fleetSetId));
        }
    }

    public function delete(Order $order): void
    {
        $this->orderRepository->remove($order, true);
    }
}

