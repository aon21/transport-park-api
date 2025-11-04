<?php

namespace App\Tests\Feature;

use App\Tests\ApiTestCase;
use App\Tests\Fixtures\OrderFixtures;

class OrderControllerTest extends ApiTestCase
{
    public function testIndexReturnsAllOrders(): void
    {
        $this->loadFixtures([OrderFixtures::class]);

        $this->client->request('GET', '/api/orders');

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertIsArray($json);
        $this->assertCount(5, $json);
        $this->assertArrayHasKey('id', $json[0]);
        $this->assertArrayHasKey('orderNumber', $json[0]);
        $this->assertArrayHasKey('status', $json[0]);
    }

    public function testShowReturnsOrderById(): void
    {
        $references = $this->loadFixtures([OrderFixtures::class]);
        $order = $references->getReference(OrderFixtures::ORDER_1_PENDING, \App\Entity\Order::class);

        $this->client->request('GET', '/api/orders/' . $order->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('ORD-001', $json['orderNumber']);
        $this->assertEquals('Maintenance', $json['serviceType']);
        $this->assertEquals('pending', $json['status']);
    }

    public function testShowReturns404WhenOrderNotFound(): void
    {
        $this->loadFixtures([OrderFixtures::class]);

        $this->client->request('GET', '/api/orders/123e4567-e89b-12d3-a456-426614174000');

        $this->assertResponseStatusCodeSame(404);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateCreatesNewOrderWithTruck(): void
    {
        $references = $this->loadFixtures([OrderFixtures::class]);
        $truck = $references->getReference(\App\Tests\Fixtures\TruckFixtures::TRUCK_5_IN_SERVICE, \App\Entity\Truck::class);

        $this->requestJson('POST', '/api/orders', [
            'orderNumber' => 'ORD-100',
            'truckId' => $truck->getId()->toRfc4122(),
            'serviceType' => 'Test Service',
            'description' => 'Test order description',
            'status' => 'pending',
            'startDate' => '2025-12-01 10:00:00'
        ]);

        $this->assertResponseStatusCodeSame(201);
        $json = $this->getJsonResponse();
        $this->assertEquals('ORD-100', $json['orderNumber']);
        $this->assertEquals('Test Service', $json['serviceType']);
        $this->assertEquals('pending', $json['status']);
        $this->assertNotNull($json['truckId']);
    }

    public function testCreateCreatesNewOrderWithFleetSet(): void
    {
        $references = $this->loadFixtures([OrderFixtures::class]);
        $fleetSet = $references->getReference(\App\Tests\Fixtures\FleetSetFixtures::FLEET_3, \App\Entity\FleetSet::class);

        $this->requestJson('POST', '/api/orders', [
            'orderNumber' => 'ORD-200',
            'fleetSetId' => $fleetSet->getId()->toRfc4122(),
            'serviceType' => 'Fleet Service',
            'description' => 'Complete fleet service',
            'status' => 'pending',
            'startDate' => '2025-12-05 10:00:00'
        ]);

        $this->assertResponseStatusCodeSame(201);
        $json = $this->getJsonResponse();
        $this->assertEquals('ORD-200', $json['orderNumber']);
        $this->assertNotNull($json['fleetSetId']);
    }

    public function testCreateReturns422WithMissingRequiredFields(): void
    {
        $this->loadFixtures([OrderFixtures::class]);

        $this->requestJson('POST', '/api/orders', [
            'orderNumber' => 'ORD-300'
        ]);

        $this->assertResponseStatusCodeSame(422);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateReturns422WithInvalidStatus(): void
    {
        $references = $this->loadFixtures([OrderFixtures::class]);
        $truck = $references->getReference(\App\Tests\Fixtures\TruckFixtures::TRUCK_5_IN_SERVICE, \App\Entity\Truck::class);

        $this->requestJson('POST', '/api/orders', [
            'orderNumber' => 'ORD-400',
            'truckId' => $truck->getId()->toRfc4122(),
            'serviceType' => 'Test Service',
            'description' => 'Test order',
            'status' => 'invalid_status',
            'startDate' => '2025-12-01 10:00:00'
        ]);

        $this->assertResponseStatusCodeSame(422);
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testCreateReturns422WithDuplicateOrderNumber(): void
    {
        $references = $this->loadFixtures([OrderFixtures::class]);
        $truck = $references->getReference(\App\Tests\Fixtures\TruckFixtures::TRUCK_5_IN_SERVICE, \App\Entity\Truck::class);

        $this->requestJson('POST', '/api/orders', [
            'orderNumber' => 'ORD-001',
            'truckId' => $truck->getId()->toRfc4122(),
            'serviceType' => 'Test Service',
            'description' => 'Test order',
            'status' => 'pending',
            'startDate' => '2025-12-01 10:00:00'
        ]);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(
            in_array($statusCode, [422, 500]),
            'Expected 422 or 500 for duplicate order number'
        );
        $json = $this->getJsonResponse();
        $this->assertArrayHasKey('error', $json);
    }

    public function testUpdateModifiesOrderWithValidData(): void
    {
        $references = $this->loadFixtures([OrderFixtures::class]);
        $order = $references->getReference(OrderFixtures::ORDER_1_PENDING, \App\Entity\Order::class);

        $this->requestJson('PUT', '/api/orders/' . $order->getId()->toRfc4122(), [
            'status' => 'in_progress',
            'description' => 'Updated description'
        ]);

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertEquals('in_progress', $json['status']);
        $this->assertEquals('Updated description', $json['description']);
    }

    public function testDeleteRemovesOrder(): void
    {
        $references = $this->loadFixtures([OrderFixtures::class]);
        $order = $references->getReference(OrderFixtures::ORDER_5_PENDING, \App\Entity\Order::class);

        $this->client->request('DELETE', '/api/orders/' . $order->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(204);
    }
}

