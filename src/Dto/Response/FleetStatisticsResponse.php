<?php

namespace App\Dto\Response;

readonly class FleetStatisticsResponse
{
    public function __construct(
        public int   $total,
        public int   $works,
        public int   $free,
        public int   $downtime,
        public int   $available,
        public float $utilizationRate
    ) {
    }

    public static function fromGroupedFleetSets(array $grouped, int $total): self
    {
        $works = count($grouped['works']);
        $free = count($grouped['free']);

        return new self(
            total: $total,
            works: $works,
            free: $free,
            downtime: count($grouped['downtime']),
            available: $works + $free,
            utilizationRate: $total > 0
                ? round(($works / $total) * 100, 2)
                : 0.0
        );
    }

    /**
     * Create from raw statistics array (optimized for database aggregation)
     * 
     * @param array{total: int, works: int, free: int, downtime: int} $stats
     */
    public static function fromArray(array $stats): self
    {
        $works = $stats['works'];
        $free = $stats['free'];
        $total = $stats['total'];

        return new self(
            total: $total,
            works: $works,
            free: $free,
            downtime: $stats['downtime'],
            available: $works + $free,
            utilizationRate: $total > 0
                ? round(($works / $total) * 100, 2)
                : 0.0
        );
    }
}

