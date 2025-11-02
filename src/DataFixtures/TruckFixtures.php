<?php

namespace App\DataFixtures;

use App\Entity\Truck;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TruckFixtures extends Fixture
{
    public const TRUCK_OPERATIONAL_1 = 'truck-operational-1';
    public const TRUCK_OPERATIONAL_2 = 'truck-operational-2';
    public const TRUCK_OPERATIONAL_3 = 'truck-operational-3';
    public const TRUCK_IN_SERVICE_1 = 'truck-in-service-1';
    public const TRUCK_IN_SERVICE_2 = 'truck-in-service-2';

    public function load(ObjectManager $manager): void
    {
        $trucks = [
            [
                'registrationNumber' => 'ABC-1234',
                'brand' => 'Volvo',
                'model' => 'FH16',
                'status' => 'operational',
                'reference' => self::TRUCK_OPERATIONAL_1,
            ],
            [
                'registrationNumber' => 'DEF-5678',
                'brand' => 'Scania',
                'model' => 'R500',
                'status' => 'operational',
                'reference' => self::TRUCK_OPERATIONAL_2,
            ],
            [
                'registrationNumber' => 'GHI-9012',
                'brand' => 'Mercedes-Benz',
                'model' => 'Actros',
                'status' => 'operational',
                'reference' => self::TRUCK_OPERATIONAL_3,
            ],
            [
                'registrationNumber' => 'JKL-3456',
                'brand' => 'MAN',
                'model' => 'TGX',
                'status' => 'in_service',
                'reference' => self::TRUCK_IN_SERVICE_1,
            ],
            [
                'registrationNumber' => 'MNO-7890',
                'brand' => 'DAF',
                'model' => 'XF',
                'status' => 'in_service',
                'reference' => self::TRUCK_IN_SERVICE_2,
            ],
        ];

        foreach ($trucks as $truckData) {
            $truck = new Truck();
            $truck->setRegistrationNumber($truckData['registrationNumber'])
                ->setBrand($truckData['brand'])
                ->setModel($truckData['model'])
                ->setStatus($truckData['status']);

            $manager->persist($truck);
            $this->addReference($truckData['reference'], $truck);
        }

        $manager->flush();
    }
}

