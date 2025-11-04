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
        $order
            ->setOrderNumber($dto->orderNumber)
            ->setServiceType($dto->serviceType)
            ->setDescription($dto->description)
            ->setStatus($dto->status)
            ->setStartDate(new DateTime($dto->startDate));

        // Handle nullable relationships (null = unassign)
        $order->setTruck($dto->truckId ? $this->truckRepository->findOrFail($dto->truckId) : null);
        $order->setTrailer($dto->trailerId ? $this->trailerRepository->findOrFail($dto->trailerId) : null);
        $order->setFleetSet($dto->fleetSetId ? $this->fleetSetRepository->findOrFail($dto->fleetSetId) : null);
        $order->setEndDate($dto->endDate ? new DateTime($dto->endDate) : null);

        $this->orderRepository->save($order, true);

        return $order;
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

