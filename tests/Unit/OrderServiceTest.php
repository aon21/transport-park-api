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
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class OrderServiceTest extends TestCase
{
    private OrderRepository|MockObject $orderRepository;
    private TruckRepository|MockObject $truckRepository;
    private TrailerRepository|MockObject $trailerRepository;
    private FleetSetRepository|MockObject $fleetSetRepository;
    private OrderService $orderService;

    /**
     * @throws Exception
     */
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

    /**
     * @throws \Exception
     */
    public function testCreateWithMinimalData(): void
    {
        $dto = new OrderCreateDto(
            orderNumber: 'ORD-001',
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: 'Maintenance',
            description: 'Regular maintenance',
            status: 'pending',
            startDate: '2025-12-01 10:00:00',
            endDate: null
        );

        $this->orderRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Order::class), true);

        $order = $this->orderService->create($dto);

        $this->assertEquals('ORD-001', $order->getOrderNumber());
        $this->assertEquals('Maintenance', $order->getServiceType());
        $this->assertEquals('Regular maintenance', $order->getDescription());
        $this->assertEquals('pending', $order->getStatus());
        $this->assertNotNull($order->getStartDate());
        $this->assertNull($order->getEndDate());
        $this->assertNull($order->getTruck());
        $this->assertNull($order->getTrailer());
        $this->assertNull($order->getFleetSet());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testCreateWithTruck(): void
    {
        $dto = new OrderCreateDto(
            orderNumber: 'ORD-002',
            truckId: 'truck-uuid',
            trailerId: null,
            fleetSetId: null,
            serviceType: 'Repair',
            description: 'Engine repair',
            status: 'in_progress',
            startDate: '2025-12-01 10:00:00',
            endDate: null
        );

        $truck = $this->createMock(Truck::class);

        $this->truckRepository->expects($this->once())
            ->method('findOrFail')
            ->with('truck-uuid')
            ->willReturn($truck);

        $this->orderRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Order::class), true);

        $order = $this->orderService->create($dto);

        $this->assertEquals('ORD-002', $order->getOrderNumber());
        $this->assertSame($truck, $order->getTruck());
        $this->assertNull($order->getTrailer());
        $this->assertNull($order->getFleetSet());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testCreateWithTrailer(): void
    {
        $dto = new OrderCreateDto(
            orderNumber: 'ORD-003',
            truckId: null,
            trailerId: 'trailer-uuid',
            fleetSetId: null,
            serviceType: 'Inspection',
            description: 'Annual inspection',
            status: 'pending',
            startDate: '2025-12-01 10:00:00',
            endDate: null
        );

        $trailer = $this->createMock(Trailer::class);

        $this->trailerRepository->expects($this->once())
            ->method('findOrFail')
            ->with('trailer-uuid')
            ->willReturn($trailer);

        $this->orderRepository->expects($this->once())
            ->method('save');

        $order = $this->orderService->create($dto);

        $this->assertSame($trailer, $order->getTrailer());
        $this->assertNull($order->getTruck());
        $this->assertNull($order->getFleetSet());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testCreateWithFleetSet(): void
    {
        $dto = new OrderCreateDto(
            orderNumber: 'ORD-004',
            truckId: null,
            trailerId: null,
            fleetSetId: 'fleetset-uuid',
            serviceType: 'Full Service',
            description: 'Complete fleet service',
            status: 'pending',
            startDate: '2025-12-01 10:00:00',
            endDate: null
        );

        $fleetSet = $this->createMock(FleetSet::class);

        $this->fleetSetRepository->expects($this->once())
            ->method('findOrFail')
            ->with('fleetset-uuid')
            ->willReturn($fleetSet);

        $this->orderRepository->expects($this->once())
            ->method('save');

        $order = $this->orderService->create($dto);

        $this->assertSame($fleetSet, $order->getFleetSet());
        $this->assertNull($order->getTruck());
        $this->assertNull($order->getTrailer());
    }

    /**
     * @throws Exception
     */
    public function testCreateWithAllRelationships(): void
    {
        $dto = new OrderCreateDto(
            orderNumber: 'ORD-005',
            truckId: 'truck-uuid',
            trailerId: 'trailer-uuid',
            fleetSetId: 'fleetset-uuid',
            serviceType: 'Complex Service',
            description: 'All entities involved',
            status: 'pending',
            startDate: '2025-12-01 10:00:00',
            endDate: null
        );

        $truck = $this->createMock(Truck::class);
        $trailer = $this->createMock(Trailer::class);
        $fleetSet = $this->createMock(FleetSet::class);

        $this->truckRepository->expects($this->once())
            ->method('findOrFail')
            ->with('truck-uuid')
            ->willReturn($truck);

        $this->trailerRepository->expects($this->once())
            ->method('findOrFail')
            ->with('trailer-uuid')
            ->willReturn($trailer);

        $this->fleetSetRepository->expects($this->once())
            ->method('findOrFail')
            ->with('fleetset-uuid')
            ->willReturn($fleetSet);

        $this->orderRepository->expects($this->once())
            ->method('save');

        $order = $this->orderService->create($dto);

        $this->assertSame($truck, $order->getTruck());
        $this->assertSame($trailer, $order->getTrailer());
        $this->assertSame($fleetSet, $order->getFleetSet());
    }

    /**
     * @throws \Exception
     */
    public function testCreateWithEndDate(): void
    {
        $dto = new OrderCreateDto(
            orderNumber: 'ORD-006',
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: 'Completed Service',
            description: 'Service with end date',
            status: 'completed',
            startDate: '2025-12-01 10:00:00',
            endDate: '2025-12-05 16:00:00'
        );

        $this->orderRepository->expects($this->once())
            ->method('save');

        $order = $this->orderService->create($dto);

        $this->assertNotNull($order->getStartDate());
        $this->assertNotNull($order->getEndDate());
        $this->assertEquals('2025-12-01', $order->getStartDate()->format('Y-m-d'));
        $this->assertEquals('2025-12-05', $order->getEndDate()->format('Y-m-d'));
    }

    /**
     * @throws \Exception
     */
    public function testUpdateOrderNumber(): void
    {
        $order = new Order();
        $order->setOrderNumber('ORD-OLD');

        $dto = new OrderUpdateDto(
            orderNumber: 'ORD-NEW',
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: null,
            description: null,
            status: null,
            startDate: null,
            endDate: null
        );

        $this->orderRepository->expects($this->once())
            ->method('save')
            ->with($order, true);

        $result = $this->orderService->update($order, $dto);

        $this->assertEquals('ORD-NEW', $result->getOrderNumber());
    }

    /**
     * @throws \Exception
     */
    public function testUpdateServiceType(): void
    {
        $order = new Order();
        $order->setServiceType('Old Service');

        $dto = new OrderUpdateDto(
            orderNumber: null,
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: 'New Service',
            description: null,
            status: null,
            startDate: null,
            endDate: null
        );

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertEquals('New Service', $result->getServiceType());
    }

    /**
     * @throws \Exception
     */
    public function testUpdateDescription(): void
    {
        $order = new Order();
        $order->setDescription('Old description');

        $dto = new OrderUpdateDto(
            orderNumber: null,
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: null,
            description: 'New description',
            status: null,
            startDate: null,
            endDate: null
        );

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertEquals('New description', $result->getDescription());
    }

    /**
     * @throws \Exception
     */
    public function testUpdateStatus(): void
    {
        $order = new Order();
        $order->setStatus('pending');

        $dto = new OrderUpdateDto(
            orderNumber: null,
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: null,
            description: null,
            status: 'in_progress',
            startDate: null,
            endDate: null
        );

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertEquals('in_progress', $result->getStatus());
    }

    /**
     * @throws \Exception
     */
    public function testUpdateStartDate(): void
    {
        $order = new Order();
        $order->setStartDate(new DateTime('2025-01-01'));

        $dto = new OrderUpdateDto(
            orderNumber: null,
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: null,
            description: null,
            status: null,
            startDate: '2025-12-01 10:00:00',
            endDate: null
        );

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertEquals('2025-12-01', $result->getStartDate()->format('Y-m-d'));
    }

    /**
     * @throws \Exception
     */
    public function testUpdateEndDate(): void
    {
        $order = new Order();

        $dto = new OrderUpdateDto(
            orderNumber: null,
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: null,
            description: null,
            status: null,
            startDate: null,
            endDate: '2025-12-10 18:00:00'
        );

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertNotNull($result->getEndDate());
        $this->assertEquals('2025-12-10', $result->getEndDate()->format('Y-m-d'));
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testUpdateTruck(): void
    {
        $order = new Order();

        $dto = new OrderUpdateDto(
            orderNumber: null,
            truckId: 'new-truck-uuid',
            trailerId: null,
            fleetSetId: null,
            serviceType: null,
            description: null,
            status: null,
            startDate: null,
            endDate: null
        );

        $truck = $this->createMock(Truck::class);

        $this->truckRepository->expects($this->once())
            ->method('findOrFail')
            ->with('new-truck-uuid')
            ->willReturn($truck);

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertSame($truck, $result->getTruck());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testUpdateTrailer(): void
    {
        $order = new Order();

        $dto = new OrderUpdateDto(
            orderNumber: null,
            truckId: null,
            trailerId: 'new-trailer-uuid',
            fleetSetId: null,
            serviceType: null,
            description: null,
            status: null,
            startDate: null,
            endDate: null
        );

        $trailer = $this->createMock(Trailer::class);

        $this->trailerRepository->expects($this->once())
            ->method('findOrFail')
            ->with('new-trailer-uuid')
            ->willReturn($trailer);

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertSame($trailer, $result->getTrailer());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testUpdateFleetSet(): void
    {
        $order = new Order();

        $dto = new OrderUpdateDto(
            orderNumber: null,
            truckId: null,
            trailerId: null,
            fleetSetId: 'new-fleetset-uuid',
            serviceType: null,
            description: null,
            status: null,
            startDate: null,
            endDate: null
        );

        $fleetSet = $this->createMock(FleetSet::class);

        $this->fleetSetRepository->expects($this->once())
            ->method('findOrFail')
            ->with('new-fleetset-uuid')
            ->willReturn($fleetSet);

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertSame($fleetSet, $result->getFleetSet());
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithNullValues(): void
    {
        $order = new Order();
        $order->setOrderNumber('ORD-001');
        $order->setServiceType('Service');
        $order->setDescription('Description');
        $order->setStatus('pending');

        $dto = new OrderUpdateDto(
            orderNumber: null,
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: null,
            description: null,
            status: null,
            startDate: null,
            endDate: null
        );

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertEquals('ORD-001', $result->getOrderNumber());
        $this->assertEquals('Service', $result->getServiceType());
        $this->assertEquals('Description', $result->getDescription());
        $this->assertEquals('pending', $result->getStatus());
    }

    /**
     * @throws \Exception
     */
    public function testUpdateMultipleFields(): void
    {
        $order = new Order();
        $order->setOrderNumber('ORD-OLD');
        $order->setStatus('pending');
        $order->setDescription('Old description');

        $dto = new OrderUpdateDto(
            orderNumber: 'ORD-NEW',
            truckId: null,
            trailerId: null,
            fleetSetId: null,
            serviceType: null,
            description: 'New description',
            status: 'completed',
            startDate: null,
            endDate: null
        );

        $this->orderRepository->expects($this->once())
            ->method('save');

        $result = $this->orderService->update($order, $dto);

        $this->assertEquals('ORD-NEW', $result->getOrderNumber());
        $this->assertEquals('completed', $result->getStatus());
        $this->assertEquals('New description', $result->getDescription());
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

