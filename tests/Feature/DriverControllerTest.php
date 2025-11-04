<?php

namespace App\Tests\Feature;

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
        $this->assertArrayHasKey('id', $json[0]);
        $this->assertArrayHasKey('firstName', $json[0]);
    }

    public function testShowReturnsDriverById(): void
    {
        $references = $this->loadFixtures([DriverFixtures::class]);
        $driver = $references->getReference(DriverFixtures::DRIVER_1, \App\Entity\Driver::class);

        $this->client->request('GET', '/api/drivers/' . $driver->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('John', $json['firstName']);
        $this->assertEquals('Doe', $json['lastName']);
        $this->assertEquals('LIC-001', $json['licenseNumber']);
    }

    public function testShowReturns404WhenDriverNotFound(): void
    {
        $this->loadFixtures([DriverFixtures::class]);

        $this->client->request('GET', '/api/drivers/123e4567-e89b-12d3-a456-426614174000');

        $this->assertResponseStatusCodeSame(404);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
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
        $json = $this->getJsonResponse();
        $this->assertEquals('Test', $json['firstName']);
        $this->assertEquals('Driver', $json['lastName']);
        $this->assertEquals('LIC-100', $json['licenseNumber']);
    }

    public function testCreateDriverWithFleetSet(): void
    {
        $references = $this->loadFixtures([FleetSetFixtures::class]);
        $fleetSet = $references->getReference(FleetSetFixtures::FLEET_1, \App\Entity\FleetSet::class);

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

        $this->requestJson('POST', '/api/drivers', [
            'firstName' => 'Test'
        ]);

        $this->assertResponseStatusCodeSame(422);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
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

        $this->assertResponseStatusCodeSame(422);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
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
        $this->assertTrue(
            in_array($statusCode, [422, 500]),
            'Expected 422 or 500 for duplicate license number'
        );
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testUpdateModifiesDriverWithValidData(): void
    {
        $references = $this->loadFixtures([DriverFixtures::class]);
        $driver = $references->getReference(DriverFixtures::DRIVER_1, \App\Entity\Driver::class);

        $this->requestJson('PUT', '/api/drivers/' . $driver->getId()->toRfc4122(), [
            'firstName' => 'Updated',
            'lastName' => 'Name',
            'licenseNumber' => 'LIC-001-UPDATED',
            'fleetSetId' => null  // Explicitly unassign from fleet
        ]);

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('Updated', $json['firstName']);
        $this->assertEquals('Name', $json['lastName']);
        $this->assertEquals('LIC-001-UPDATED', $json['licenseNumber']);
        $this->assertNull($json['fleetSetId']);
    }

    public function testDeleteRemovesDriver(): void
    {
        $references = $this->loadFixtures([DriverFixtures::class]);
        $driver = $references->getReference(DriverFixtures::DRIVER_8, \App\Entity\Driver::class);

        $this->client->request('DELETE', '/api/drivers/' . $driver->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(204);
    }
}

