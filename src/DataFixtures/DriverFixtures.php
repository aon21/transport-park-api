<?php

namespace App\DataFixtures;

use App\Entity\Driver;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DriverFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $drivers = [
            // Assigned to Fleet Alpha (will make it "works" status)
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
            // Assigned to Fleet Gamma (downtime, has drivers but truck in service)
            [
                'firstName' => 'Ahmed',
                'lastName' => 'Hassan',
                'licenseNumber' => 'DL-003456',
                'fleetSet' => FleetSetFixtures::FLEET_SET_DOWNTIME_TRUCK,
            ],
            // Assigned to Fleet Delta (downtime, has driver but trailer in service)
            [
                'firstName' => 'Emma',
                'lastName' => 'Johnson',
                'licenseNumber' => 'DL-004567',
                'fleetSet' => FleetSetFixtures::FLEET_SET_DOWNTIME_TRAILER,
            ],
            // Unassigned drivers
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
                $driver->setFleetSet($this->getReference($driverData['fleetSet'], \App\Entity\FleetSet::class));
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

