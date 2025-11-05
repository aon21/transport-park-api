<?php

namespace App\Tests\Fixtures;

use App\Entity\FleetSet;
use App\Entity\Trailer;
use App\Entity\Truck;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FleetSetFixtures extends Fixture implements DependentFixtureInterface
{
    public const FLEET_1 = 'fleet_1';
    public const FLEET_2 = 'fleet_2';
    public const FLEET_3 = 'fleet_3';
    public const FLEET_4_DOWNTIME = 'fleet_4_downtime';

    public function load(ObjectManager $manager): void
    {
        $fleet1 = new FleetSet();
        $fleet1->setName('Fleet Alpha');
        $fleet1->setTruck($this->getReference(TruckFixtures::TRUCK_1, Truck::class));
        $fleet1->setTrailer($this->getReference(TrailerFixtures::TRAILER_1, Trailer::class));
        $manager->persist($fleet1);
        $this->addReference(self::FLEET_1, $fleet1);

        $fleet2 = new FleetSet();
        $fleet2->setName('Fleet Beta');
        $fleet2->setTruck($this->getReference(TruckFixtures::TRUCK_2, Truck::class));
        $fleet2->setTrailer($this->getReference(TrailerFixtures::TRAILER_2, Trailer::class));
        $manager->persist($fleet2);
        $this->addReference(self::FLEET_2, $fleet2);

        $fleet3 = new FleetSet();
        $fleet3->setName('Fleet Gamma');
        $fleet3->setTruck($this->getReference(TruckFixtures::TRUCK_3, Truck::class));
        $fleet3->setTrailer($this->getReference(TrailerFixtures::TRAILER_3, Trailer::class));
        $manager->persist($fleet3);
        $this->addReference(self::FLEET_3, $fleet3);

        $fleet4 = new FleetSet();
        $fleet4->setName('Fleet Delta');
        $fleet4->setTruck($this->getReference(TruckFixtures::TRUCK_4_IN_SERVICE, Truck::class));
        $fleet4->setTrailer($this->getReference(TrailerFixtures::TRAILER_4, Trailer::class));
        $manager->persist($fleet4);
        $this->addReference(self::FLEET_4_DOWNTIME, $fleet4);

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

