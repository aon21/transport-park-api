<?php

namespace App\DataFixtures;

use App\Entity\Driver;
use App\Entity\FleetSet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DriverFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $drivers = [
            [
                'firstName' => 'John',
                'lastName' => 'Smith',
                'licenseNumber' => 'DL-001234',
                'fleetSet' => FleetSetFixtures::FLEET_SET_WORKS,
            ],
            [
                'firstName' => 'Maria',
                'lastName' => 'Garcia',
                'licenseNumber' => 'DL-002345',
                'fleetSet' => FleetSetFixtures::FLEET_SET_WORKS,
            ],
            [
                'firstName' => 'Ahmed',
                'lastName' => 'Hassan',
                'licenseNumber' => 'DL-003456',
                'fleetSet' => FleetSetFixtures::FLEET_SET_DOWNTIME_TRUCK,
            ],
            [
                'firstName' => 'Emma',
                'lastName' => 'Johnson',
                'licenseNumber' => 'DL-004567',
                'fleetSet' => FleetSetFixtures::FLEET_SET_DOWNTIME_TRAILER,
            ],
            [
                'firstName' => 'Robert',
                'lastName' => 'Brown',
                'licenseNumber' => 'DL-005678',
                'fleetSet' => null,
            ],
            [
                'firstName' => 'Sofia',
                'lastName' => 'Martinez',
                'licenseNumber' => 'DL-006789',
                'fleetSet' => null,
            ],
            [
                'firstName' => 'Michael',
                'lastName' => 'Davis',
                'licenseNumber' => 'DL-007890',
                'fleetSet' => null,
            ],
            [
                'firstName' => 'Yuki',
                'lastName' => 'Tanaka',
                'licenseNumber' => 'DL-008901',
                'fleetSet' => null,
            ],
        ];

        foreach ($drivers as $driverData) {
            $driver = new Driver();
            $driver->setFirstName($driverData['firstName'])
                ->setLastName($driverData['lastName'])
                ->setLicenseNumber($driverData['licenseNumber']);

            if ($driverData['fleetSet'] !== null) {
                $driver->setFleetSet($this->getReference($driverData['fleetSet'], FleetSet::class));
            }

            $manager->persist($driver);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FleetSetFixtures::class,
        ];
    }
}

