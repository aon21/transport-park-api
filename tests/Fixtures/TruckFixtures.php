<?php

namespace App\Tests\Fixtures;

use App\Entity\Truck;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TruckFixtures extends Fixture
{
    public const TRUCK_1 = 'truck_1';
    public const TRUCK_2 = 'truck_2';
    public const TRUCK_3 = 'truck_3';
    public const TRUCK_4_IN_SERVICE = 'truck_4_in_service';
    public const TRUCK_5_IN_SERVICE = 'truck_5_in_service';

    public function load(ObjectManager $manager): void
    {
        $truck1 = new Truck();
        $truck1->setRegistrationNumber('TRUCK-001');
        $truck1->setBrand('Volvo');
        $truck1->setModel('FH16');
        $truck1->setStatus('operational');
        $manager->persist($truck1);
        $this->addReference(self::TRUCK_1, $truck1);

        $truck2 = new Truck();
        $truck2->setRegistrationNumber('TRUCK-002');
        $truck2->setBrand('Scania');
        $truck2->setModel('R500');
        $truck2->setStatus('operational');
        $manager->persist($truck2);
        $this->addReference(self::TRUCK_2, $truck2);

        $truck3 = new Truck();
        $truck3->setRegistrationNumber('TRUCK-003');
        $truck3->setBrand('Mercedes');
        $truck3->setModel('Actros');
        $truck3->setStatus('operational');
        $manager->persist($truck3);
        $this->addReference(self::TRUCK_3, $truck3);

        $truck4 = new Truck();
        $truck4->setRegistrationNumber('TRUCK-004');
        $truck4->setBrand('MAN');
        $truck4->setModel('TGX');
        $truck4->setStatus('in_service');
        $manager->persist($truck4);
        $this->addReference(self::TRUCK_4_IN_SERVICE, $truck4);

        $truck5 = new Truck();
        $truck5->setRegistrationNumber('TRUCK-005');
        $truck5->setBrand('DAF');
        $truck5->setModel('XF');
        $truck5->setStatus('in_service');
        $manager->persist($truck5);
        $this->addReference(self::TRUCK_5_IN_SERVICE, $truck5);

        $manager->flush();
    }
}

