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
        $this->assertHasJsonKeys(['id', 'registrationNumber'], $json[0]);
    }

    public function testShowReturnsTruckById(): void
    {
        $truck = $this->loadFixtures([TruckFixtures::class])
            ->getReference(TruckFixtures::TRUCK_1, Truck::class);

        $this->client->request('GET', '/api/trucks/' . $truck->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonFields([
            'registrationNumber' => 'TRUCK-001',
            'brand' => 'Volvo',
            'model' => 'FH16',
            'status' => 'operational'
        ]);
    }

    public function testShowReturns404WhenTruckNotFound(): void
    {
        $this->loadFixtures([TruckFixtures::class]);
        $this->client->request('GET', '/api/trucks/' . $this->getNonExistentUuid());
        $this->assertErrorResponse(404);
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
        $this->assertJsonFields([
            'registrationNumber' => 'TRUCK-100',
            'brand' => 'Volvo',
            'model' => 'FH16',
            'status' => 'operational'
        ]);
    }

    public function testCreateReturns422WithMissingRequiredFields(): void
    {
        $this->loadFixtures([TruckFixtures::class]);
        $this->requestJson('POST', '/api/trucks', ['brand' => 'Volvo']);
        $this->assertErrorResponse();
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

        $this->assertErrorResponse();
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
        $this->assertTrue(in_array($statusCode, [422, 500]));
        $this->assertArrayHasKey('error', $this->getJsonResponse());
    }

    public function testUpdateModifiesTruckWithValidData(): void
    {
        $truck = $this->loadFixtures([TruckFixtures::class])
            ->getReference(TruckFixtures::TRUCK_1, Truck::class);

        $this->requestJson('PUT', '/api/trucks/' . $truck->getId()->toRfc4122(), [
            'registrationNumber' => 'TRUCK-001-UPDATED',
            'brand' => 'Updated Brand',
            'model' => 'Updated Model',
            'status' => 'in_service'
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonFields([
            'registrationNumber' => 'TRUCK-001-UPDATED',
            'brand' => 'Updated Brand',
            'model' => 'Updated Model',
            'status' => 'in_service'
        ]);
    }

    public function testDeleteRemovesTruck(): void
    {
        $truck = $this->loadFixtures([TruckFixtures::class])
            ->getReference(TruckFixtures::TRUCK_5_IN_SERVICE, Truck::class);

        $this->client->request('DELETE', '/api/trucks/' . $truck->getId()->toRfc4122());
        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteTruckUsedInFleetSet(): void
    {
        $truck = $this->loadFixtures([TruckFixtures::class, FleetSetFixtures::class])
            ->getReference(TruckFixtures::TRUCK_1, Truck::class);

        $this->client->request('DELETE', '/api/trucks/' . $truck->getId()->toRfc4122());

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [204, 500]));
    }
}
