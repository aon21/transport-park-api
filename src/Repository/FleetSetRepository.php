<?php

namespace App\Repository;

use App\Entity\FleetSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FleetSet>
 */
class FleetSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FleetSet::class);
    }

    public function save(FleetSet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FleetSet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find all fleet sets with their trucks, trailers, and drivers loaded
     *
     * @return FleetSet[]
     */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('fs')
            ->leftJoin('fs.truck', 't')
            ->leftJoin('fs.trailer', 'tr')
            ->leftJoin('fs.drivers', 'd')
            ->addSelect('t', 'tr', 'd')
            ->orderBy('fs.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find fleet sets by truck
     *
     * @return FleetSet[]
     */
    public function findByTruck(string $truckId): array
    {
        return $this->createQueryBuilder('fs')
            ->andWhere('fs.truck = :truckId')
            ->setParameter('truckId', $truckId)
            ->orderBy('fs.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find fleet sets by trailer
     *
     * @return FleetSet[]
     */
    public function findByTrailer(string $trailerId): array
    {
        return $this->createQueryBuilder('fs')
            ->andWhere('fs.trailer = :trailerId')
            ->setParameter('trailerId', $trailerId)
            ->orderBy('fs.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
