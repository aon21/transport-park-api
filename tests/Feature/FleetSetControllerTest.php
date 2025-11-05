<?php

namespace App\Tests\Feature;

use App\Entity\FleetSet;
use App\Entity\Trailer;
use App\Entity\Truck;
use App\Tests\ApiTestCase;
use App\Tests\Fixtures\FleetSetFixtures;
use App\Tests\Fixtures\TrailerFixtures;
use App\Tests\Fixtures\TruckFixtures;

class FleetSetControllerTest extends ApiTestCase
{
    public function testIndexReturnsAllFleetSets(): void
    {
        $this->loadFixtures([FleetSetFixtures::class]);

        $this->client->request('GET', '/api/fleet-sets');

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertIsArray($json);
        $this->assertCount(4, $json);
        $this->assertHasJsonKeys(['id', 'name', 'truckId', 'trailerId'], $json[0]);
    }

    public function testShowReturnsFleetSetById(): void
    {
        $fleetSet = $this->loadFixtures([FleetSetFixtures::class])
            ->getReference(FleetSetFixtures::FLEET_1, FleetSet::class);

        $this->client->request('GET', '/api/fleet-sets/' . $fleetSet->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('Fleet Alpha', $json['name']);
        $this->assertHasJsonKeys(['truckId', 'trailerId', 'status'], $json);
    }

    public function testShowReturns404WhenFleetSetNotFound(): void
    {
        $this->loadFixtures([FleetSetFixtures::class]);
        $this->client->request('GET', '/api/fleet-sets/' . $this->getNonExistentUuid());
        $this->assertErrorResponse(404);
    }

    public function testCreateCreatesNewFleetSetWithValidData(): void
    {
        $references = $this->loadFixtures([FleetSetFixtures::class]);
        $truck = $references->getReference(TruckFixtures::TRUCK_5_IN_SERVICE, Truck::class);
        $trailer = $references->getReference(TrailerFixtures::TRAILER_5_IN_SERVICE, Trailer::class);

        $this->requestJson('POST', '/api/fleet-sets', [
            'name' => 'Fleet Epsilon',
            'truckId' => $truck->getId()->toRfc4122(),
            'trailerId' => $trailer->getId()->toRfc4122()
        ]);

        $this->assertResponseStatusCodeSame(201);
        $json = $this->getJsonResponse();
        $this->assertEquals('Fleet Epsilon', $json['name']);
        $this->assertEquals($truck->getId()->toRfc4122(), $json['truckId']);
        $this->assertEquals($trailer->getId()->toRfc4122(), $json['trailerId']);
    }

    public function testCreateReturns422WithMissingRequiredFields(): void
    {
        $this->loadFixtures([FleetSetFixtures::class]);
        $this->requestJson('POST', '/api/fleet-sets', ['name' => 'Incomplete Fleet']);
        $this->assertErrorResponse();
    }

    public function testCreateReturns404WithInvalidTruckId(): void
    {
        $trailer = $this->loadFixtures([FleetSetFixtures::class])
            ->getReference(TrailerFixtures::TRAILER_5_IN_SERVICE, Trailer::class);

        $this->requestJson('POST', '/api/fleet-sets', [
            'name' => 'Invalid Truck Fleet',
            'truckId' => $this->getNonExistentUuid(),
            'trailerId' => $trailer->getId()->toRfc4122()
        ]);

        $this->assertErrorResponse(404);
    }

    public function testCreateReturns404WithInvalidTrailerId(): void
    {
        $truck = $this->loadFixtures([FleetSetFixtures::class])
            ->getReference(TruckFixtures::TRUCK_5_IN_SERVICE, Truck::class);

        $this->requestJson('POST', '/api/fleet-sets', [
            'name' => 'Invalid Trailer Fleet',
            'truckId' => $truck->getId()->toRfc4122(),
            'trailerId' => $this->getNonExistentUuid()
        ]);

        $this->assertErrorResponse(404);
    }

    public function testUpdateModifiesFleetSetWithValidData(): void
    {
        $references = $this->loadFixtures([FleetSetFixtures::class]);
        $fleetSet = $references->getReference(FleetSetFixtures::FLEET_1, FleetSet::class);
        $truck = $references->getReference(TruckFixtures::TRUCK_1, Truck::class);
        $trailer = $references->getReference(TrailerFixtures::TRAILER_2, Trailer::class);

        $this->requestJson('PUT', '/api/fleet-sets/' . $fleetSet->getId()->toRfc4122(), [
            'name' => 'Updated Fleet Name',
            'truckId' => $truck->getId()->toRfc4122(),
            'trailerId' => $trailer->getId()->toRfc4122()
        ]);

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('Updated Fleet Name', $json['name']);
        $this->assertEquals($truck->getId()->toRfc4122(), $json['truckId']);
        $this->assertEquals($trailer->getId()->toRfc4122(), $json['trailerId']);
    }

    public function testDeleteRemovesFleetSet(): void
    {
        $fleetSet = $this->loadFixtures([FleetSetFixtures::class])
            ->getReference(FleetSetFixtures::FLEET_3, FleetSet::class);

        $this->client->request('DELETE', '/api/fleet-sets/' . $fleetSet->getId()->toRfc4122());
        $this->assertResponseStatusCodeSame(204);
    }

    public function testStatisticsReturnsFleetStatistics(): void
    {
        $this->loadFixtures([FleetSetFixtures::class]);

        $this->client->request('GET', '/api/fleet-sets/statistics');

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertHasJsonKeys(['total', 'works', 'free', 'downtime', 'available', 'utilizationRate'], $json);
        $this->assertEquals(4, $json['total']);
    }
}
