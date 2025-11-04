<?php

namespace App\Tests\Feature;

use App\Entity\Truck;
use App\Tests\ApiTestCase;
use App\Tests\Fixtures\FleetSetFixtures;
use App\Tests\Fixtures\TruckFixtures;

class TruckControllerTest extends ApiTestCase
{
    public function testIndexReturnsAllTrucks(): void
    {
        $this->loadFixtures([TruckFixtures::class]);

        $this->client->request('GET', '/api/trucks');

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertIsArray($json);
        $this->assertCount(5, $json);
        $this->assertArrayHasKey('id', $json[0]);
        $this->assertArrayHasKey('registrationNumber', $json[0]);
    }

    public function testShowReturnsTruckById(): void
    {
        $references = $this->loadFixtures([TruckFixtures::class]);
        $truck = $references->getReference(TruckFixtures::TRUCK_1, Truck::class);

        $this->client->request('GET', '/api/trucks/' . $truck->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('TRUCK-001', $json['registrationNumber']);
        $this->assertEquals('Volvo', $json['brand']);
        $this->assertEquals('FH16', $json['model']);
        $this->assertEquals('operational', $json['status']);
    }

    public function testShowReturns404WhenTruckNotFound(): void
    {
        $this->loadFixtures([TruckFixtures::class]);

        $this->client->request('GET', '/api/trucks/123e4567-e89b-12d3-a456-426614174000');

        $this->assertResponseStatusCodeSame(404);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateCreatesNewTruckWithValidData(): void
    {
        $this->loadFixtures([TruckFixtures::class]);

        $this->requestJson('POST', '/api/trucks', [
            'registrationNumber' => 'TRUCK-100',
            'brand' => 'Volvo',
            'model' => 'FH16',
            'status' => 'operational'
        ]);

        $this->assertResponseStatusCodeSame(201);
        $json = $this->getJsonResponse();
        $this->assertEquals('TRUCK-100', $json['registrationNumber']);
        $this->assertEquals('Volvo', $json['brand']);
        $this->assertEquals('FH16', $json['model']);
        $this->assertEquals('operational', $json['status']);
    }

    public function testCreateReturns422WithMissingRequiredFields(): void
    {
        $this->loadFixtures([TruckFixtures::class]);

        $this->requestJson('POST', '/api/trucks', [
            'brand' => 'Volvo'
        ]);

        $this->assertResponseStatusCodeSame(422);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateReturns422WithInvalidStatus(): void
    {
        $this->loadFixtures([TruckFixtures::class]);

        $this->requestJson('POST', '/api/trucks', [
            'registrationNumber' => 'TRUCK-300',
            'brand' => 'Volvo',
            'model' => 'FH16',
            'status' => 'invalid_status'
        ]);

        $this->assertResponseStatusCodeSame(422);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateReturns422WithDuplicateRegistrationNumber(): void
    {
        $this->loadFixtures([TruckFixtures::class]);

        $this->requestJson('POST', '/api/trucks', [
            'registrationNumber' => 'TRUCK-001',
            'brand' => 'Volvo',
            'model' => 'FH16',
            'status' => 'operational'
        ]);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($statusCode, [422, 500]),
            'Expected 422 or 500 for duplicate registration number'
        );
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testUpdateModifiesTruckWithValidData(): void
    {
        $references = $this->loadFixtures([TruckFixtures::class]);
        $truck = $references->getReference(TruckFixtures::TRUCK_1, Truck::class);

        $this->requestJson('PUT', '/api/trucks/' . $truck->getId()->toRfc4122(), [
            'registrationNumber' => 'TRUCK-001-UPDATED',
            'brand' => 'Updated Brand',
            'model' => 'Updated Model',
            'status' => 'in_service'
        ]);

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('TRUCK-001-UPDATED', $json['registrationNumber']);
        $this->assertEquals('Updated Brand', $json['brand']);
        $this->assertEquals('Updated Model', $json['model']);
        $this->assertEquals('in_service', $json['status']);
    }

    public function testDeleteRemovesTruck(): void
    {
        $references = $this->loadFixtures([TruckFixtures::class]);
        $truck = $references->getReference(TruckFixtures::TRUCK_5_IN_SERVICE, Truck::class);

        $this->client->request('DELETE', '/api/trucks/' . $truck->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteTruckUsedInFleetSet(): void
    {
        $references = $this->loadFixtures([TruckFixtures::class, FleetSetFixtures::class]);
        $truck = $references->getReference(TruckFixtures::TRUCK_1, Truck::class);

        $this->client->request('DELETE', '/api/trucks/' . $truck->getId()->toRfc4122());

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($statusCode, [204, 500]),
            "Expected 204 (SQLite) or 500 (FK enforced), got {$statusCode}"
        );
    }
}
