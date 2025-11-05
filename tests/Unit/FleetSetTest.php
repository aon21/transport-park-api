<?php

namespace App\Tests\Unit;

use App\Entity\Driver;
use App\Entity\FleetSet;
use App\Entity\Trailer;
use App\Entity\Truck;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class FleetSetTest extends TestCase
{
    /**
     * @throws Exception
     */
    private function createFleetSet(string $truckStatus = 'operational', string $trailerStatus = 'operational'): FleetSet
    {
        $truck = $this->createMock(Truck::class);
        $truck->method('getStatus')->willReturn($truckStatus);

        $trailer = $this->createMock(Trailer::class);
        $trailer->method('getStatus')->willReturn($trailerStatus);

        $fleetSet = new FleetSet();
        $fleetSet->setTruck($truck);
        $fleetSet->setTrailer($trailer);

        return $fleetSet;
    }

    /**
     * @throws Exception
     */
    public function testGetStatusReturnsDowntimeWhenTruckInService(): void
    {
        $fleetSet = $this->createFleetSet('in_service', 'operational');

        $this->assertEquals(FleetSet::STATUS_DOWNTIME, $fleetSet->getStatus());
    }

    /**
     * @throws Exception
     */
    public function testGetStatusReturnsDowntimeWhenTrailerInService(): void
    {
        $fleetSet = $this->createFleetSet('operational', 'in_service');

        $this->assertEquals(FleetSet::STATUS_DOWNTIME, $fleetSet->getStatus());
    }

    /**
     * @throws Exception
     */
    public function testGetStatusReturnsDowntimeWhenBothInService(): void
    {
        $fleetSet = $this->createFleetSet('in_service', 'in_service');

        $this->assertEquals(FleetSet::STATUS_DOWNTIME, $fleetSet->getStatus());
    }

    /**
     * @throws Exception
     */
    public function testGetStatusReturnsWorksWhenHasDrivers(): void
    {
        $fleetSet = $this->createFleetSet();

        $driver = $this->createMock(Driver::class);
        $fleetSet->getDrivers()->add($driver);

        $this->assertEquals(FleetSet::STATUS_WORKS, $fleetSet->getStatus());
    }

    public function testGetStatusReturnsWorksWithMultipleDrivers(): void
    {
        $fleetSet = $this->createFleetSet();

        $fleetSet->getDrivers()->add($this->createMock(Driver::class));
        $fleetSet->getDrivers()->add($this->createMock(Driver::class));

        $this->assertEquals(FleetSet::STATUS_WORKS, $fleetSet->getStatus());
    }

    public function testGetStatusReturnsFreeWhenNoDrivers(): void
    {
        $fleetSet = $this->createFleetSet();

        $this->assertEquals(FleetSet::STATUS_FREE, $fleetSet->getStatus());
    }

    public function testGetStatusPrioritizesDowntimeOverDrivers(): void
    {
        $fleetSet = $this->createFleetSet('in_service', 'operational');

        $driver = $this->createMock(Driver::class);
        $fleetSet->getDrivers()->add($driver);

        $this->assertEquals(FleetSet::STATUS_DOWNTIME, $fleetSet->getStatus());
    }

    public function testStatusConstants(): void
    {
        $this->assertEquals('downtime', FleetSet::STATUS_DOWNTIME);
        $this->assertEquals('works', FleetSet::STATUS_WORKS);
        $this->assertEquals('free', FleetSet::STATUS_FREE);
    }
}

