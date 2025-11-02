<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\TruckRepository;
use App\Repository\TrailerRepository;
use App\Repository\FleetSetRepository;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private TruckRepository $truckRepository,
        private TrailerRepository $trailerRepository,
        private FleetSetRepository $fleetSetRepository
    ) {
    }

    public function findAll(?string $status = null): array
    {
        return $status 
            ? $this->orderRepository->findByStatus($status)
            : $this->orderRepository->findAllWithRelations();
    }

    public function findActive(): array
    {
        return $this->orderRepository->findActive();
    }

    public function findById(string $id): ?Order
    {
        return $this->orderRepository->find($id);
    }

    public function create(array $data): Order
    {
        $order = new Order();
        $order->setOrderNumber($data['orderNumber'])
            ->setServiceType($data['serviceType'])
            ->setDescription($data['description'])
            ->setStatus($data['status'])
            ->setStartDate(new \DateTime($data['startDate']));

        if (isset($data['endDate'])) {
            $order->setEndDate(new \DateTime($data['endDate']));
        }

        if (isset($data['truckId'])) {
            $truck = $this->truckRepository->find($data['truckId']);
            if ($truck) {
                $order->setTruck($truck);
            }
        }

        if (isset($data['trailerId'])) {
            $trailer = $this->trailerRepository->find($data['trailerId']);
            if ($trailer) {
                $order->setTrailer($trailer);
            }
        }

        if (isset($data['fleetSetId'])) {
            $fleetSet = $this->fleetSetRepository->find($data['fleetSetId']);
            if ($fleetSet) {
                $order->setFleetSet($fleetSet);
            }
        }

        $this->orderRepository->save($order, true);

        return $order;
    }

    public function update(Order $order, array $data): Order
    {
        if (isset($data['orderNumber'])) {
            $order->setOrderNumber($data['orderNumber']);
        }
        if (isset($data['serviceType'])) {
            $order->setServiceType($data['serviceType']);
        }
        if (isset($data['description'])) {
            $order->setDescription($data['description']);
        }
        if (isset($data['status'])) {
            $order->setStatus($data['status']);
        }
        if (isset($data['startDate'])) {
            $order->setStartDate(new \DateTime($data['startDate']));
        }
        if (isset($data['endDate'])) {
            $order->setEndDate(new \DateTime($data['endDate']));
        }

        if (isset($data['truckId'])) {
            $truck = $this->truckRepository->find($data['truckId']);
            $order->setTruck($truck);
        }
        if (isset($data['trailerId'])) {
            $trailer = $this->trailerRepository->find($data['trailerId']);
            $order->setTrailer($trailer);
        }
        if (isset($data['fleetSetId'])) {
            $fleetSet = $this->fleetSetRepository->find($data['fleetSetId']);
            $order->setFleetSet($fleetSet);
        }

        $this->orderRepository->save($order, true);

        return $order;
    }

    public function delete(Order $order): void
    {
        $this->orderRepository->remove($order, true);
    }
}

