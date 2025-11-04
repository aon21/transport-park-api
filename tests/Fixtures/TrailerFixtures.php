<?php

namespace App\Tests\Fixtures;

use App\Entity\Trailer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrailerFixtures extends Fixture
{
    public const TRAILER_1 = 'trailer_1';
    public const TRAILER_2 = 'trailer_2';
    public const TRAILER_3 = 'trailer_3';
    public const TRAILER_4 = 'trailer_4';
    public const TRAILER_5_IN_SERVICE = 'trailer_5_in_service';

    public function load(ObjectManager $manager): void
    {
        $trailer1 = new Trailer();
        $trailer1->setRegistrationNumber('TRAILER-001');
        $trailer1->setType('Refrigerated');
        $trailer1->setCapacity('25.5');
        $trailer1->setStatus('operational');
        $manager->persist($trailer1);
        $this->addReference(self::TRAILER_1, $trailer1);

        $trailer2 = new Trailer();
        $trailer2->setRegistrationNumber('TRAILER-002');
        $trailer2->setType('Flatbed');
        $trailer2->setCapacity('30.0');
        $trailer2->setStatus('operational');
        $manager->persist($trailer2);
        $this->addReference(self::TRAILER_2, $trailer2);

        $trailer3 = new Trailer();
        $trailer3->setRegistrationNumber('TRAILER-003');
        $trailer3->setType('Box');
        $trailer3->setCapacity('28.0');
        $trailer3->setStatus('operational');
        $manager->persist($trailer3);
        $this->addReference(self::TRAILER_3, $trailer3);

        $trailer4 = new Trailer();
        $trailer4->setRegistrationNumber('TRAILER-004');
        $trailer4->setType('Tanker');
        $trailer4->setCapacity('35.0');
        $trailer4->setStatus('operational');
        $manager->persist($trailer4);
        $this->addReference(self::TRAILER_4, $trailer4);

        $trailer5 = new Trailer();
        $trailer5->setRegistrationNumber('TRAILER-005');
        $trailer5->setType('Lowboy');
        $trailer5->setCapacity('20.0');
        $trailer5->setStatus('in_service');
        $manager->persist($trailer5);
        $this->addReference(self::TRAILER_5_IN_SERVICE, $trailer5);

        $manager->flush();
    }
}

