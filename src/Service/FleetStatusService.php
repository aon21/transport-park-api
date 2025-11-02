<?php

namespace App\Service;

use App\Entity\FleetSet;
use App\Repository\OrderRepository;

class FleetStatusService
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {
    }

    /**
     * Calculate the status of a fleet set, considering active service orders
     * 
     * Status Logic:
     * - 'downtime': truck OR trailer is in_service, OR has active service orders
     * - 'works': both operational AND has drivers AND no active orders
     * - 'free': both operational AND no drivers AND no active orders
     */
    public function calculateStatus(FleetSet $fleetSet): string
    {
        // Check if truck or trailer is in service
        if ($fleetSet->getTruck()->getStatus() === 'in_service' || 
            $fleetSet->getTrailer()->getStatus() === 'in_service') {
            return 'downtime';
        }

        // Check if fleet set has any active service orders
        $activeOrders = $this->orderRepository->findActiveForFleetSet($fleetSet->getId());
        if (!empty($activeOrders)) {
            return 'downtime';
        }

        // Both truck and trailer are operational and no active orders
        // Check if has drivers
        if ($fleetSet->getDrivers()->count() > 0) {
            return 'works';
        }

        return 'free';
    }

    /**
     * Check if a fleet set is available for assignment
     */
    public function isAvailable(FleetSet $fleetSet): bool
    {
        return $this->calculateStatus($fleetSet) !== 'downtime';
    }

    /**
     * Get all fleet sets grouped by status
     * 
     * @param FleetSet[] $fleetSets
     * @return array<string, FleetSet[]>
     */
    public function groupByStatus(array $fleetSets): array
    {
        $grouped = [
            'works' => [],
            'free' => [],
            'downtime' => [],
        ];

        foreach ($fleetSets as $fleetSet) {
            $status = $this->calculateStatus($fleetSet);
            $grouped[$status][] = $fleetSet;
        }

        return $grouped;
    }

    /**
     * Get statistics about fleet statuses
     * 
     * @param FleetSet[] $fleetSets
     */
    public function getStatistics(array $fleetSets): array
    {
        $grouped = $this->groupByStatus($fleetSets);

        return [
            'total' => count($fleetSets),
            'works' => count($grouped['works']),
            'free' => count($grouped['free']),
            'downtime' => count($grouped['downtime']),
            'available' => count($grouped['works']) + count($grouped['free']),
            'utilization_rate' => count($fleetSets) > 0 
                ? round((count($grouped['works']) / count($fleetSets)) * 100, 2) 
                : 0,
        ];
    }
}

