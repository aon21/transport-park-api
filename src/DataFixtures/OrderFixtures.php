<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Order 1: Pending - truck service
        $order1 = new Order();
        $order1->setOrderNumber('ORD-2024-001')
            ->setServiceType('Routine Maintenance')
            ->setDescription('Annual maintenance service for truck ABC-1234')
            ->setStatus('pending')
            ->setStartDate(new \DateTime('+2 days'))
            ->setEndDate(new \DateTime('+3 days'))
            ->setTruck($this->getReference(TruckFixtures::TRUCK_OPERATIONAL_1, \App\Entity\Truck::class));
        $manager->persist($order1);

        // Order 2: In Progress - trailer service
        $order2 = new Order();
        $order2->setOrderNumber('ORD-2024-002')
            ->setServiceType('Brake System Repair')
            ->setDescription('Complete brake system overhaul for trailer TRL-1005')
            ->setStatus('in_progress')
            ->setStartDate(new \DateTime('-1 day'))
            ->setEndDate(null) // Still in progress
            ->setTrailer($this->getReference(TrailerFixtures::TRAILER_IN_SERVICE, \App\Entity\Trailer::class));
        $manager->persist($order2);

        // Order 3: Completed - fleet set service
        $order3 = new Order();
        $order3->setOrderNumber('ORD-2024-003')
            ->setServiceType('Pre-Trip Inspection')
            ->setDescription('Complete pre-trip inspection for Fleet Alpha before long haul')
            ->setStatus('completed')
            ->setStartDate(new \DateTime('-7 days'))
            ->setEndDate(new \DateTime('-6 days'))
            ->setFleetSet($this->getReference(FleetSetFixtures::FLEET_SET_WORKS, \App\Entity\FleetSet::class));
        $manager->persist($order3);

        // Order 4: Cancelled - individual truck
        $order4 = new Order();
        $order4->setOrderNumber('ORD-2024-004')
            ->setServiceType('Tire Replacement')
            ->setDescription('Tire replacement service - cancelled due to rescheduling')
            ->setStatus('cancelled')
            ->setStartDate(new \DateTime('-3 days'))
            ->setEndDate(null)
            ->setTruck($this->getReference(TruckFixtures::TRUCK_IN_SERVICE_1, \App\Entity\Truck::class));
        $manager->persist($order4);

        // Order 5: Pending - entire fleet set
        $order5 = new Order();
        $order5->setOrderNumber('ORD-2024-005')
            ->setServiceType('Full Fleet Inspection')
            ->setDescription('Complete inspection of Fleet Beta before certification renewal')
            ->setStatus('pending')
            ->setStartDate(new \DateTime('+5 days'))
            ->setEndDate(new \DateTime('+7 days'))
            ->setFleetSet($this->getReference(FleetSetFixtures::FLEET_SET_FREE, \App\Entity\FleetSet::class));
        $manager->persist($order5);

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

