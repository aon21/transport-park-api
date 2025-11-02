<?php

namespace App\DataFixtures;

use App\Entity\Trailer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrailerFixtures extends Fixture
{
    public const TRAILER_OPERATIONAL_1 = 'trailer-operational-1';
    public const TRAILER_OPERATIONAL_2 = 'trailer-operational-2';
    public const TRAILER_OPERATIONAL_3 = 'trailer-operational-3';
    public const TRAILER_OPERATIONAL_4 = 'trailer-operational-4';
    public const TRAILER_IN_SERVICE = 'trailer-in-service';

    public function load(ObjectManager $manager): void
    {
        $trailers = [
            [
                'registrationNumber' => 'TRL-1001',
                'type' => 'Refrigerated',
                'capacity' => 25.00,
                'status' => 'operational',
                'reference' => self::TRAILER_OPERATIONAL_1,
            ],
            [
                'registrationNumber' => 'TRL-1002',
                'type' => 'Dry Van',
                'capacity' => 30.00,
                'status' => 'operational',
                'reference' => self::TRAILER_OPERATIONAL_2,
            ],
            [
                'registrationNumber' => 'TRL-1003',
                'type' => 'Flatbed',
                'capacity' => 28.50,
                'status' => 'operational',
                'reference' => self::TRAILER_OPERATIONAL_3,
            ],
            [
                'registrationNumber' => 'TRL-1004',
                'type' => 'Tanker',
                'capacity' => 35.00,
                'status' => 'operational',
                'reference' => self::TRAILER_OPERATIONAL_4,
            ],
            [
                'registrationNumber' => 'TRL-1005',
                'type' => 'Refrigerated',
                'capacity' => 26.00,
                'status' => 'in_service',
                'reference' => self::TRAILER_IN_SERVICE,
            ],
        ];

        foreach ($trailers as $trailerData) {
            $trailer = new Trailer();
            $trailer->setRegistrationNumber($trailerData['registrationNumber'])
                ->setType($trailerData['type'])
                ->setCapacity($trailerData['capacity'])
                ->setStatus($trailerData['status']);

            $manager->persist($trailer);
            $this->addReference($trailerData['reference'], $trailer);
        }

        $manager->flush();
    }
}

