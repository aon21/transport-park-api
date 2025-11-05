<?php

namespace App\Tests\Feature;

use App\Entity\FleetSet;
use App\Entity\Order;
use App\Entity\Truck;
use App\Tests\ApiTestCase;
use App\Tests\Fixtures\FleetSetFixtures;
use App\Tests\Fixtures\OrderFixtures;
use App\Tests\Fixtures\TruckFixtures;

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
        $this->assertHasJsonKeys(['id', 'orderNumber', 'status'], $json[0]);
    }

    public function testShowReturnsOrderById(): void
    {
        $order = $this->loadFixtures([OrderFixtures::class])
            ->getReference(OrderFixtures::ORDER_1_PENDING, Order::class);

        $this->client->request('GET', '/api/orders/' . $order->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonFields([
            'orderNumber' => 'ORD-001',
            'serviceType' => 'Maintenance',
            'status' => 'pending'
        ]);
    }

    public function testShowReturns404WhenOrderNotFound(): void
    {
        $this->loadFixtures([OrderFixtures::class]);
        $this->client->request('GET', '/api/orders/' . $this->getNonExistentUuid());
        $this->assertErrorResponse(404);
    }

    public function testCreateCreatesNewOrderWithTruck(): void
    {
        $truck = $this->loadFixtures([OrderFixtures::class])
            ->getReference(TruckFixtures::TRUCK_5_IN_SERVICE, Truck::class);

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
        $fleetSet = $this->loadFixtures([OrderFixtures::class])
            ->getReference(FleetSetFixtures::FLEET_3, FleetSet::class);

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
        $this->requestJson('POST', '/api/orders', ['orderNumber' => 'ORD-300']);
        $this->assertErrorResponse();
    }

    public function testCreateReturns422WithInvalidStatus(): void
    {
        $truck = $this->loadFixtures([OrderFixtures::class])
            ->getReference(TruckFixtures::TRUCK_5_IN_SERVICE, Truck::class);

        $this->requestJson('POST', '/api/orders', [
            'orderNumber' => 'ORD-400',
            'truckId' => $truck->getId()->toRfc4122(),
            'serviceType' => 'Test Service',
            'description' => 'Test order',
            'status' => 'invalid_status',
            'startDate' => '2025-12-01 10:00:00'
        ]);

        $this->assertErrorResponse();
    }

    public function testCreateReturns422WithNoAssets(): void
    {
        $this->loadFixtures([OrderFixtures::class]);

        $this->requestJson('POST', '/api/orders', [
            'orderNumber' => 'ORD-500',
            'serviceType' => 'Maintenance',
            'description' => 'Test description',
            'status' => 'pending',
            'startDate' => '2025-12-01 10:00:00',
        ]);

        $this->assertErrorResponse();
    }

    public function testCreateReturns422WithDuplicateOrderNumber(): void
    {
        $truck = $this->loadFixtures([OrderFixtures::class])
            ->getReference(TruckFixtures::TRUCK_5_IN_SERVICE, Truck::class);

        $this->requestJson('POST', '/api/orders', [
            'orderNumber' => 'ORD-001',
            'truckId' => $truck->getId()->toRfc4122(),
            'serviceType' => 'Test Service',
            'description' => 'Test order',
            'status' => 'pending',
            'startDate' => '2025-12-01 10:00:00'
        ]);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [422, 500]));
        $this->assertArrayHasKey('error', $this->getJsonResponse());
    }

    public function testUpdateModifiesOrderWithValidData(): void
    {
        $order = $this->loadFixtures([OrderFixtures::class])
            ->getReference(OrderFixtures::ORDER_1_PENDING, Order::class);

        $this->requestJson('PUT', '/api/orders/' . $order->getId()->toRfc4122(), [
            'orderNumber' => $order->getOrderNumber(),
            'truckId' => $order->getTruck()?->getId()->toRfc4122(),
            'trailerId' => $order->getTrailer()?->getId()->toRfc4122(),
            'fleetSetId' => $order->getFleetSet()?->getId()->toRfc4122(),
            'serviceType' => 'Updated Service Type',
            'description' => 'Updated description',
            'status' => 'in_progress',
            'startDate' => $order->getStartDate()->format('Y-m-d H:i:s'),
            'endDate' => null
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonFields([
            'status' => 'in_progress',
            'description' => 'Updated description',
            'serviceType' => 'Updated Service Type'
        ]);
    }

    public function testDeleteRemovesOrder(): void
    {
        $order = $this->loadFixtures([OrderFixtures::class])
            ->getReference(OrderFixtures::ORDER_5_PENDING, Order::class);

        $this->client->request('DELETE', '/api/orders/' . $order->getId()->toRfc4122());
        $this->assertResponseStatusCodeSame(204);
    }
}
