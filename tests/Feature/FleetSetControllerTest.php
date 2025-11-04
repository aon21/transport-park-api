<?php

namespace App\Tests\Feature;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\FleetSetFixtures;

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
        $this->assertArrayHasKey('id', $json[0]);
        $this->assertArrayHasKey('name', $json[0]);
        $this->assertArrayHasKey('truckId', $json[0]);
        $this->assertArrayHasKey('trailerId', $json[0]);
    }

    public function testShowReturnsFleetSetById(): void
    {
        $references = $this->loadFixtures([FleetSetFixtures::class]);
        $fleetSet = $references->getReference(FleetSetFixtures::FLEET_1, \App\Entity\FleetSet::class);

        $this->client->request('GET', '/api/fleet-sets/' . $fleetSet->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('Fleet Alpha', $json['name']);
        $this->assertArrayHasKey('truckId', $json);
        $this->assertArrayHasKey('trailerId', $json);
        $this->assertArrayHasKey('status', $json);
    }

    public function testShowReturns404WhenFleetSetNotFound(): void
    {
        $this->loadFixtures([FleetSetFixtures::class]);

        $this->client->request('GET', '/api/fleet-sets/123e4567-e89b-12d3-a456-426614174000');

        $this->assertResponseStatusCodeSame(404);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateCreatesNewFleetSetWithValidData(): void
    {
        $references = $this->loadFixtures([FleetSetFixtures::class]);
        $truck = $references->getReference(\App\Tests\Fixtures\TruckFixtures::TRUCK_5_IN_SERVICE, \App\Entity\Truck::class);
        $trailer = $references->getReference(\App\Tests\Fixtures\TrailerFixtures::TRAILER_5_IN_SERVICE, \App\Entity\Trailer::class);

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

        $this->requestJson('POST', '/api/fleet-sets', [
            'name' => 'Incomplete Fleet'
        ]);

        $this->assertResponseStatusCodeSame(422);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateReturns404WithInvalidTruckId(): void
    {
        $references = $this->loadFixtures([FleetSetFixtures::class]);
        $trailer = $references->getReference(\App\Tests\Fixtures\TrailerFixtures::TRAILER_5_IN_SERVICE, \App\Entity\Trailer::class);

        $this->requestJson('POST', '/api/fleet-sets', [
            'name' => 'Invalid Truck Fleet',
            'truckId' => '123e4567-e89b-12d3-a456-426614174000',
            'trailerId' => $trailer->getId()->toRfc4122()
        ]);

        $this->assertResponseStatusCodeSame(404);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateReturns404WithInvalidTrailerId(): void
    {
        $references = $this->loadFixtures([FleetSetFixtures::class]);
        $truck = $references->getReference(\App\Tests\Fixtures\TruckFixtures::TRUCK_5_IN_SERVICE, \App\Entity\Truck::class);

        $this->requestJson('POST', '/api/fleet-sets', [
            'name' => 'Invalid Trailer Fleet',
            'truckId' => $truck->getId()->toRfc4122(),
            'trailerId' => '123e4567-e89b-12d3-a456-426614174000'
        ]);

        $this->assertResponseStatusCodeSame(404);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testUpdateModifiesFleetSetWithValidData(): void
    {
        $references = $this->loadFixtures([FleetSetFixtures::class]);
        $fleetSet = $references->getReference(FleetSetFixtures::FLEET_1, \App\Entity\FleetSet::class);

        $this->requestJson('PUT', '/api/fleet-sets/' . $fleetSet->getId()->toRfc4122(), [
            'name' => 'Updated Fleet Name'
        ]);

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('Updated Fleet Name', $json['name']);
    }

    public function testDeleteRemovesFleetSet(): void
    {
        $references = $this->loadFixtures([FleetSetFixtures::class]);
        $fleetSet = $references->getReference(FleetSetFixtures::FLEET_3, \App\Entity\FleetSet::class);

        $this->client->request('DELETE', '/api/fleet-sets/' . $fleetSet->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(204);
    }

    public function testStatisticsReturnsFleetStatistics(): void
    {
        $this->loadFixtures([FleetSetFixtures::class]);

        $this->client->request('GET', '/api/fleet-sets/statistics');

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('total', $json);
        $this->assertArrayHasKey('works', $json);
        $this->assertArrayHasKey('free', $json);
        $this->assertArrayHasKey('downtime', $json);
        $this->assertArrayHasKey('available', $json);
        $this->assertArrayHasKey('utilizationRate', $json);
        $this->assertEquals(4, $json['total']);
    }
}

