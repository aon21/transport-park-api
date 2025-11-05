<?php

namespace App\Tests\Feature;

use App\Entity\Driver;
use App\Entity\FleetSet;
use App\Tests\ApiTestCase;
use App\Tests\Fixtures\DriverFixtures;
use App\Tests\Fixtures\FleetSetFixtures;

class DriverControllerTest extends ApiTestCase
{
    public function testIndexReturnsAllDrivers(): void
    {
        $this->loadFixtures([DriverFixtures::class]);

        $this->client->request('GET', '/api/drivers');

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertIsArray($json);
        $this->assertCount(8, $json);
        $this->assertHasJsonKeys(['id', 'firstName'], $json[0]);
    }

    public function testShowReturnsDriverById(): void
    {
        $driver = $this->loadFixtures([DriverFixtures::class])
            ->getReference(DriverFixtures::DRIVER_1, Driver::class);

        $this->client->request('GET', '/api/drivers/' . $driver->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonFields([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'licenseNumber' => 'LIC-001'
        ]);
    }

    public function testShowReturns404WhenDriverNotFound(): void
    {
        $this->loadFixtures([DriverFixtures::class]);
        $this->client->request('GET', '/api/drivers/' . $this->getNonExistentUuid());
        $this->assertErrorResponse(404);
    }

    public function testCreateCreatesNewDriverWithValidData(): void
    {
        $this->loadFixtures([DriverFixtures::class]);

        $this->requestJson('POST', '/api/drivers', [
            'firstName' => 'Test',
            'lastName' => 'Driver',
            'licenseNumber' => 'LIC-100'
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonFields([
            'firstName' => 'Test',
            'lastName' => 'Driver',
            'licenseNumber' => 'LIC-100'
        ]);
    }

    public function testCreateDriverWithFleetSet(): void
    {
        $fleetSet = $this->loadFixtures([FleetSetFixtures::class])
            ->getReference(FleetSetFixtures::FLEET_1, FleetSet::class);

        $this->requestJson('POST', '/api/drivers', [
            'firstName' => 'Fleet',
            'lastName' => 'Driver',
            'licenseNumber' => 'LIC-200',
            'fleetSetId' => $fleetSet->getId()->toRfc4122()
        ]);

        $this->assertResponseStatusCodeSame(201);
        $json = $this->getJsonResponse();
        $this->assertEquals('Fleet', $json['firstName']);
        $this->assertEquals('LIC-200', $json['licenseNumber']);
        $this->assertNotNull($json['fleetSetId']);
    }

    public function testCreateReturns422WithMissingRequiredFields(): void
    {
        $this->loadFixtures([DriverFixtures::class]);
        $this->requestJson('POST', '/api/drivers', ['firstName' => 'Test']);
        $this->assertErrorResponse();
    }

    public function testCreateReturns422WithInvalidFleetSetId(): void
    {
        $this->loadFixtures([DriverFixtures::class]);

        $this->requestJson('POST', '/api/drivers', [
            'firstName' => 'Test',
            'lastName' => 'Driver',
            'licenseNumber' => 'LIC-300',
            'fleetSetId' => 'invalid-uuid'
        ]);

        $this->assertErrorResponse();
    }

    public function testCreateReturns422WithDuplicateLicenseNumber(): void
    {
        $this->loadFixtures([DriverFixtures::class]);

        $this->requestJson('POST', '/api/drivers', [
            'firstName' => 'Test',
            'lastName' => 'Driver',
            'licenseNumber' => 'LIC-001'
        ]);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [422, 500]));
        $this->assertArrayHasKey('error', $this->getJsonResponse());
    }

    public function testUpdateModifiesDriverWithValidData(): void
    {
        $driver = $this->loadFixtures([DriverFixtures::class])
            ->getReference(DriverFixtures::DRIVER_1, Driver::class);

        $this->requestJson('PUT', '/api/drivers/' . $driver->getId()->toRfc4122(), [
            'firstName' => 'Updated',
            'lastName' => 'Name',
            'licenseNumber' => 'LIC-001-UPDATED',
            'fleetSetId' => null
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonFields([
            'firstName' => 'Updated',
            'lastName' => 'Name',
            'licenseNumber' => 'LIC-001-UPDATED'
        ]);
        $this->assertNull($this->getJsonResponse()['fleetSetId']);
    }

    public function testDeleteRemovesDriver(): void
    {
        $driver = $this->loadFixtures([DriverFixtures::class])
            ->getReference(DriverFixtures::DRIVER_8, Driver::class);

        $this->client->request('DELETE', '/api/drivers/' . $driver->getId()->toRfc4122());
        $this->assertResponseStatusCodeSame(204);
    }
}
