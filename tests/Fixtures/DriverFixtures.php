<?php

namespace App\Tests\Fixtures;

use App\Entity\Driver;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DriverFixtures extends Fixture
{
    public const DRIVER_1 = 'driver_1';
    public const DRIVER_2 = 'driver_2';
    public const DRIVER_3 = 'driver_3';
    public const DRIVER_4 = 'driver_4';
    public const DRIVER_5 = 'driver_5';
    public const DRIVER_6 = 'driver_6';
    public const DRIVER_7 = 'driver_7';
    public const DRIVER_8 = 'driver_8';

    public function load(ObjectManager $manager): void
    {
        $driver1 = new Driver();
        $driver1->setFirstName('John');
        $driver1->setLastName('Doe');
        $driver1->setLicenseNumber('LIC-001');
        $manager->persist($driver1);
        $this->addReference(self::DRIVER_1, $driver1);

        $driver2 = new Driver();
        $driver2->setFirstName('Jane');
        $driver2->setLastName('Smith');
        $driver2->setLicenseNumber('LIC-002');
        $manager->persist($driver2);
        $this->addReference(self::DRIVER_2, $driver2);

        $driver3 = new Driver();
        $driver3->setFirstName('Bob');
        $driver3->setLastName('Johnson');
        $driver3->setLicenseNumber('LIC-003');
        $manager->persist($driver3);
        $this->addReference(self::DRIVER_3, $driver3);

        $driver4 = new Driver();
        $driver4->setFirstName('Alice');
        $driver4->setLastName('Williams');
        $driver4->setLicenseNumber('LIC-004');
        $manager->persist($driver4);
        $this->addReference(self::DRIVER_4, $driver4);

        $driver5 = new Driver();
        $driver5->setFirstName('Charlie');
        $driver5->setLastName('Brown');
        $driver5->setLicenseNumber('LIC-005');
        $manager->persist($driver5);
        $this->addReference(self::DRIVER_5, $driver5);

        $driver6 = new Driver();
        $driver6->setFirstName('Diana');
        $driver6->setLastName('Davis');
        $driver6->setLicenseNumber('LIC-006');
        $manager->persist($driver6);
        $this->addReference(self::DRIVER_6, $driver6);

        $driver7 = new Driver();
        $driver7->setFirstName('Eve');
        $driver7->setLastName('Martinez');
        $driver7->setLicenseNumber('LIC-007');
        $manager->persist($driver7);
        $this->addReference(self::DRIVER_7, $driver7);

        $driver8 = new Driver();
        $driver8->setFirstName('Frank');
        $driver8->setLastName('Garcia');
        $driver8->setLicenseNumber('LIC-008');
        $manager->persist($driver8);
        $this->addReference(self::DRIVER_8, $driver8);

        $manager->flush();
    }
}

