<?php

namespace App\Tests\Fixtures;

use App\Entity\FleetSet;
use App\Entity\Order;
use App\Entity\Trailer;
use App\Entity\Truck;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public const ORDER_1_PENDING = 'order_1_pending';
    public const ORDER_2_IN_PROGRESS = 'order_2_in_progress';
    public const ORDER_3_COMPLETED = 'order_3_completed';
    public const ORDER_4_CANCELLED = 'order_4_cancelled';
    public const ORDER_5_PENDING = 'order_5_pending';

    public function load(ObjectManager $manager): void
    {
        $order1 = new Order();
        $order1->setOrderNumber('ORD-001');
        $order1->setServiceType('Maintenance');
        $order1->setDescription('Regular maintenance check');
        $order1->setStatus('pending');
        $order1->setStartDate(new \DateTime('2025-11-10'));
        $order1->setTruck($this->getReference(TruckFixtures::TRUCK_1, Truck::class));
        $manager->persist($order1);
        $this->addReference(self::ORDER_1_PENDING, $order1);

        $order2 = new Order();
        $order2->setOrderNumber('ORD-002');
        $order2->setServiceType('Repair');
        $order2->setDescription('Engine repair');
        $order2->setStatus('in_progress');
        $order2->setStartDate(new \DateTime('2025-11-05'));
        $order2->setTrailer($this->getReference(TrailerFixtures::TRAILER_1, Trailer::class));
        $manager->persist($order2);
        $this->addReference(self::ORDER_2_IN_PROGRESS, $order2);

        $order3 = new Order();
        $order3->setOrderNumber('ORD-003');
        $order3->setServiceType('Inspection');
        $order3->setDescription('Annual inspection');
        $order3->setStatus('completed');
        $order3->setStartDate(new \DateTime('2025-10-01'));
        $order3->setEndDate(new \DateTime('2025-10-05'));
        $order3->setFleetSet($this->getReference(FleetSetFixtures::FLEET_1, FleetSet::class));
        $manager->persist($order3);
        $this->addReference(self::ORDER_3_COMPLETED, $order3);

        $order4 = new Order();
        $order4->setOrderNumber('ORD-004');
        $order4->setServiceType('Upgrade');
        $order4->setDescription('GPS system upgrade');
        $order4->setStatus('cancelled');
        $order4->setStartDate(new \DateTime('2025-11-15'));
        $order4->setTruck($this->getReference(TruckFixtures::TRUCK_2, Truck::class));
        $manager->persist($order4);
        $this->addReference(self::ORDER_4_CANCELLED, $order4);

        $order5 = new Order();
        $order5->setOrderNumber('ORD-005');
        $order5->setServiceType('Full Service');
        $order5->setDescription('Complete fleet service');
        $order5->setStatus('pending');
        $order5->setStartDate(new \DateTime('2025-11-20'));
        $order5->setTruck($this->getReference(TruckFixtures::TRUCK_3, Truck::class));
        $order5->setTrailer($this->getReference(TrailerFixtures::TRAILER_3, Trailer::class));
        $order5->setFleetSet($this->getReference(FleetSetFixtures::FLEET_2, FleetSet::class));
        $manager->persist($order5);
        $this->addReference(self::ORDER_5_PENDING, $order5);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TruckFixtures::class,
            TrailerFixtures::class,
            FleetSetFixtures::class,
        ];
    }
}

