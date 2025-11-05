<?php

namespace App\Tests\Unit;

use App\Dto\Response\FleetStatisticsResponse;
use App\Entity\FleetSet;
use PHPUnit\Framework\TestCase;

class FleetStatisticsResponseTest extends TestCase
{
    private function createStats(int $total, int $works, int $free, int $downtime): array
    {
        return compact('total', 'works', 'free', 'downtime');
    }

    private function createGrouped(int $works, int $free, int $downtime): array
    {
        $mocks = array_fill(0, $works + $free + $downtime, $this->createMock(FleetSet::class));
        
        return [
            'works' => array_slice($mocks, 0, $works),
            'free' => array_slice($mocks, $works, $free),
            'downtime' => array_slice($mocks, $works + $free, $downtime)
        ];
    }

    public function testFromArrayWithNormalData(): void
    {
        $response = FleetStatisticsResponse::fromArray($this->createStats(10, 6, 2, 2));

        $this->assertEquals(10, $response->total);
        $this->assertEquals(6, $response->works);
        $this->assertEquals(2, $response->free);
        $this->assertEquals(2, $response->downtime);
        $this->assertEquals(8, $response->available);
        $this->assertEquals(60.0, $response->utilizationRate);
    }

    public function testFromArrayWithZeroTotal(): void
    {
        $response = FleetStatisticsResponse::fromArray($this->createStats(0, 0, 0, 0));

        $this->assertEquals(0, $response->total);
        $this->assertEquals(0, $response->available);
        $this->assertEquals(0.0, $response->utilizationRate);
    }

    public function testFromArrayCalculatesCorrectly(): void
    {
        $response = FleetStatisticsResponse::fromArray($this->createStats(3, 1, 1, 1));

        $this->assertEquals(2, $response->available);
        $this->assertEquals(33.33, $response->utilizationRate);
    }

    public function testFromArrayEdgeCases(): void
    {
        $allWorking = FleetStatisticsResponse::fromArray($this->createStats(5, 5, 0, 0));
        $this->assertEquals(100.0, $allWorking->utilizationRate);

        $allDowntime = FleetStatisticsResponse::fromArray($this->createStats(5, 0, 0, 5));
        $this->assertEquals(0.0, $allDowntime->utilizationRate);
    }

    public function testFromGroupedFleetSetsWithNormalData(): void
    {
        $response = FleetStatisticsResponse::fromGroupedFleetSets($this->createGrouped(2, 1, 1), 4);

        $this->assertEquals(4, $response->total);
        $this->assertEquals(2, $response->works);
        $this->assertEquals(1, $response->free);
        $this->assertEquals(1, $response->downtime);
        $this->assertEquals(3, $response->available);
        $this->assertEquals(50.0, $response->utilizationRate);
    }

    public function testFromGroupedFleetSetsWithEmptyGroups(): void
    {
        $response = FleetStatisticsResponse::fromGroupedFleetSets($this->createGrouped(0, 0, 0), 0);

        $this->assertEquals(0, $response->total);
        $this->assertEquals(0.0, $response->utilizationRate);
    }

    public function testFromGroupedFleetSetsEdgeCases(): void
    {
        $allWorking = FleetStatisticsResponse::fromGroupedFleetSets($this->createGrouped(3, 0, 0), 3);
        $this->assertEquals(100.0, $allWorking->utilizationRate);

        $allFree = FleetStatisticsResponse::fromGroupedFleetSets($this->createGrouped(0, 2, 0), 2);
        $this->assertEquals(0.0, $allFree->utilizationRate);
        $this->assertEquals(2, $allFree->available);
    }
}

