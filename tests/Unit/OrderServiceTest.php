<?php

namespace App\Tests\Unit;

use App\Dto\OrderCreateDto;
use App\Dto\OrderUpdateDto;
use App\Entity\FleetSet;
use App\Entity\Order;
use App\Entity\Trailer;
use App\Entity\Truck;
use App\Repository\FleetSetRepository;
use App\Repository\OrderRepository;
use App\Repository\TrailerRepository;
use App\Repository\TruckRepository;
use App\Service\OrderService;
use DateTime;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class OrderServiceTest extends TestCase
{
    private OrderRepository|MockObject $orderRepository;
    private TruckRepository|MockObject $truckRepository;
    private TrailerRepository|MockObject $trailerRepository;
    private FleetSetRepository|MockObject $fleetSetRepository;
    private OrderService $orderService;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->truckRepository = $this->createMock(TruckRepository::class);
        $this->trailerRepository = $this->createMock(TrailerRepository::class);
        $this->fleetSetRepository = $this->createMock(FleetSetRepository::class);

        $this->orderService = new OrderService(
            $this->orderRepository,
            $this->truckRepository,
            $this->trailerRepository,
            $this->fleetSetRepository
        );
    }

    private function createDto(
        string $orderNumber = 'ORD-001',
        ?string $truckId = null,
        ?string $trailerId = null,
        ?string $fleetSetId = null,
        string $serviceType = 'Test Service',
        string $description = 'Test description',
        string $status = 'pending',
        string $startDate = '2025-12-01 10:00:00',
        ?string $endDate = null
    ): OrderCreateDto {
        return new OrderCreateDto(
            orderNumber: $orderNumber,
            truckId: $truckId,
            trailerId: $trailerId,
            fleetSetId: $fleetSetId,
            serviceType: $serviceType,
            description: $description,
            status: $status,
            startDate: $startDate,
            endDate: $endDate
        );
    }

    private function createUpdateDto(
        string $orderNumber = 'ORD-001',
        ?string $truckId = null,
        ?string $trailerId = null,
        ?string $fleetSetId = null,
        string $serviceType = 'Test Service',
        string $description = 'Test description',
        string $status = 'pending',
        string $startDate = '2025-12-01 10:00:00',
        ?string $endDate = null
    ): OrderUpdateDto {
        return new OrderUpdateDto(
            orderNumber: $orderNumber,
            truckId: $truckId,
            trailerId: $trailerId,
            fleetSetId: $fleetSetId,
            serviceType: $serviceType,
            description: $description,
            status: $status,
            startDate: $startDate,
            endDate: $endDate
        );
    }

    public function testCreateWithMinimalData(): void
    {
        $dto = $this->createDto(serviceType: 'Maintenance', description: 'Regular maintenance');
        
        $this->orderRepository->expects($this->once())->method('save');
        $order = $this->orderService->create($dto);

        $this->assertEquals('ORD-001', $order->getOrderNumber());
        $this->assertEquals('Maintenance', $order->getServiceType());
        $this->assertNotNull($order->getStartDate());
        $this->assertNull($order->getEndDate());
        $this->assertNull($order->getTruck());
    }

    public function testCreateWithRelationships(): void
    {
        $truck = $this->createMock(Truck::class);
        $trailer = $this->createMock(Trailer::class);
        $fleetSet = $this->createMock(FleetSet::class);

        $this->truckRepository->method('findOrFail')->willReturn($truck);
        $this->trailerRepository->method('findOrFail')->willReturn($trailer);
        $this->fleetSetRepository->method('findOrFail')->willReturn($fleetSet);
        $this->orderRepository->expects($this->exactly(4))->method('save');

        $orderTruck = $this->orderService->create($this->createDto(truckId: 'truck-uuid'));
        $this->assertSame($truck, $orderTruck->getTruck());

        $orderTrailer = $this->orderService->create($this->createDto(trailerId: 'trailer-uuid'));
        $this->assertSame($trailer, $orderTrailer->getTrailer());

        $orderFleet = $this->orderService->create($this->createDto(fleetSetId: 'fleet-uuid'));
        $this->assertSame($fleetSet, $orderFleet->getFleetSet());

        $orderAll = $this->orderService->create($this->createDto('ORD-999', 'truck-uuid', 'trailer-uuid', 'fleet-uuid'));
        $this->assertSame($truck, $orderAll->getTruck());
        $this->assertSame($trailer, $orderAll->getTrailer());
        $this->assertSame($fleetSet, $orderAll->getFleetSet());
    }

    public function testCreateWithEndDate(): void
    {
        $dto = $this->createDto(endDate: '2025-12-05 16:00:00');
        $this->orderRepository->expects($this->once())->method('save');

        $order = $this->orderService->create($dto);

        $this->assertNotNull($order->getEndDate());
        $this->assertEquals('2025-12-05', $order->getEndDate()->format('Y-m-d'));
    }

    public function testUpdateFields(): void
    {
        $this->orderRepository->expects($this->exactly(6))->method('save');

        $order = new Order();
        $result = $this->orderService->update($order, $this->createUpdateDto(orderNumber: 'ORD-NEW'));
        $this->assertEquals('ORD-NEW', $result->getOrderNumber());

        $result = $this->orderService->update($order, $this->createUpdateDto(serviceType: 'New Service'));
        $this->assertEquals('New Service', $result->getServiceType());

        $result = $this->orderService->update($order, $this->createUpdateDto(description: 'New desc'));
        $this->assertEquals('New desc', $result->getDescription());

        $result = $this->orderService->update($order, $this->createUpdateDto(status: 'completed'));
        $this->assertEquals('completed', $result->getStatus());

        $result = $this->orderService->update($order, $this->createUpdateDto(startDate: '2025-12-01 10:00:00'));
        $this->assertEquals('2025-12-01', $result->getStartDate()->format('Y-m-d'));

        $result = $this->orderService->update($order, $this->createUpdateDto(endDate: '2025-12-10 18:00:00'));
        $this->assertEquals('2025-12-10', $result->getEndDate()->format('Y-m-d'));
    }

    public function testUpdateRelationships(): void
    {
        $truck = $this->createMock(Truck::class);
        $trailer = $this->createMock(Trailer::class);
        $fleetSet = $this->createMock(FleetSet::class);

        $this->truckRepository->method('findOrFail')->willReturn($truck);
        $this->trailerRepository->method('findOrFail')->willReturn($trailer);
        $this->fleetSetRepository->method('findOrFail')->willReturn($fleetSet);
        $this->orderRepository->expects($this->exactly(3))->method('save');

        $order = new Order();

        $result = $this->orderService->update($order, $this->createUpdateDto(truckId: 'truck-uuid'));
        $this->assertSame($truck, $result->getTruck());

        $result = $this->orderService->update($order, $this->createUpdateDto(trailerId: 'trailer-uuid'));
        $this->assertSame($trailer, $result->getTrailer());

        $result = $this->orderService->update($order, $this->createUpdateDto(fleetSetId: 'fleet-uuid'));
        $this->assertSame($fleetSet, $result->getFleetSet());
    }

    public function testUpdateMultipleFields(): void
    {
        $this->orderRepository->expects($this->once())->method('save');

        $order = new Order();
        $dto = $this->createUpdateDto('ORD-NEW', description: 'New desc', status: 'completed');
        $result = $this->orderService->update($order, $dto);

        $this->assertEquals('ORD-NEW', $result->getOrderNumber());
        $this->assertEquals('completed', $result->getStatus());
        $this->assertEquals('New desc', $result->getDescription());
    }

    public function testDelete(): void
    {
        $order = new Order();

        $this->orderRepository->expects($this->once())
            ->method('remove')
            ->with($order, true);

        $this->orderService->delete($order);

        $this->assertTrue(true);
    }
}

