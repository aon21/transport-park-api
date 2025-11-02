<?php

namespace App\DataFixtures;

use App\Entity\FleetSet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FleetSetFixtures extends Fixture implements DependentFixtureInterface
{
    public const FLEET_SET_WORKS = 'fleet-set-works';
    public const FLEET_SET_FREE = 'fleet-set-free';
    public const FLEET_SET_DOWNTIME_TRUCK = 'fleet-set-downtime-truck';
    public const FLEET_SET_DOWNTIME_TRAILER = 'fleet-set-downtime-trailer';

    public function load(ObjectManager $manager): void
    {
        // Fleet Set 1: "works" status (operational truck + operational trailer + will have drivers)
        $fleetSet1 = new FleetSet();
        $fleetSet1->setName('Fleet Alpha')
            ->setTruck($this->getReference(TruckFixtures::TRUCK_OPERATIONAL_1, \App\Entity\Truck::class))
            ->setTrailer($this->getReference(TrailerFixtures::TRAILER_OPERATIONAL_1, \App\Entity\Trailer::class));
        $manager->persist($fleetSet1);
        $this->addReference(self::FLEET_SET_WORKS, $fleetSet1);

        // Fleet Set 2: "free" status (operational truck + operational trailer, no drivers)
        $fleetSet2 = new FleetSet();
        $fleetSet2->setName('Fleet Beta')
            ->setTruck($this->getReference(TruckFixtures::TRUCK_OPERATIONAL_2, \App\Entity\Truck::class))
            ->setTrailer($this->getReference(TrailerFixtures::TRAILER_OPERATIONAL_2, \App\Entity\Trailer::class));
        $manager->persist($fleetSet2);
        $this->addReference(self::FLEET_SET_FREE, $fleetSet2);

        // Fleet Set 3: "downtime" status (truck in service)
        $fleetSet3 = new FleetSet();
        $fleetSet3->setName('Fleet Gamma')
            ->setTruck($this->getReference(TruckFixtures::TRUCK_IN_SERVICE_1, \App\Entity\Truck::class))
            ->setTrailer($this->getReference(TrailerFixtures::TRAILER_OPERATIONAL_3, \App\Entity\Trailer::class));
        $manager->persist($fleetSet3);
        $this->addReference(self::FLEET_SET_DOWNTIME_TRUCK, $fleetSet3);

        // Fleet Set 4: "downtime" status (trailer in service)
        $fleetSet4 = new FleetSet();
        $fleetSet4->setName('Fleet Delta')
            ->setTruck($this->getReference(TruckFixtures::TRUCK_OPERATIONAL_3, \App\Entity\Truck::class))
            ->setTrailer($this->getReference(TrailerFixtures::TRAILER_IN_SERVICE, \App\Entity\Trailer::class));
        $manager->persist($fleetSet4);
        $this->addReference(self::FLEET_SET_DOWNTIME_TRAILER, $fleetSet4);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TruckFixtures::class,
            TrailerFixtures::class,
        ];
    }
}

