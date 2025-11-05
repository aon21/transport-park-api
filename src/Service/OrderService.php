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
            ->setStartDate(new DateTime($dto->startDate))
            ->setEndDate($dto->endDate ? new DateTime($dto->endDate) : null)
            ->setTruck($dto->truckId ? $this->truckRepository->findOrFail($dto->truckId) : null)
            ->setTrailer($dto->trailerId ? $this->trailerRepository->findOrFail($dto->trailerId) : null)
            ->setFleetSet($dto->fleetSetId ? $this->fleetSetRepository->findOrFail($dto->fleetSetId) : null);

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
            ->setStartDate(new DateTime($dto->startDate))
            ->setEndDate($dto->endDate ? new DateTime($dto->endDate) : null)
            ->setTruck($dto->truckId ? $this->truckRepository->findOrFail($dto->truckId) : null)
            ->setTrailer($dto->trailerId ? $this->trailerRepository->findOrFail($dto->trailerId) : null)
            ->setFleetSet($dto->fleetSetId ? $this->fleetSetRepository->findOrFail($dto->fleetSetId) : null);

        $this->orderRepository->save($order, true);

        return $order;
    }

    public function delete(Order $order): void
    {
        $this->orderRepository->remove($order, true);
    }
}

