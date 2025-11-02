<?php

namespace App\Repository;

use App\Entity\FleetSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<FleetSet>
 */
class FleetSetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FleetSet::class);
    }

    /**
     * Save a fleet set entity
     */
    public function save(FleetSet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a fleet set entity
     */
    public function remove(FleetSet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find all fleet sets with their relationships loaded (truck, trailer, drivers)
     * 
     * @return FleetSet[]
     */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.truck', 't')
            ->leftJoin('f.trailer', 'tr')
            ->leftJoin('f.drivers', 'd')
            ->addSelect('t', 'tr', 'd')
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find fleet sets by truck
     * 
     * @return FleetSet[]
     */
    public function findByTruck(Uuid $truckId): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.truck = :truckId')
            ->setParameter('truckId', $truckId, 'uuid')
            ->leftJoin('f.drivers', 'd')
            ->addSelect('d')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find fleet sets by trailer
     * 
     * @return FleetSet[]
     */
    public function findByTrailer(Uuid $trailerId): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.trailer = :trailerId')
            ->setParameter('trailerId', $trailerId, 'uuid')
            ->leftJoin('f.drivers', 'd')
            ->addSelect('d')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find fleet sets with at least one driver
     * 
     * @return FleetSet[]
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.drivers', 'd')
            ->addSelect('d')
            ->having('COUNT(d.id) > 0')
            ->groupBy('f.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find fleet sets with no drivers
     * 
     * @return FleetSet[]
     */
    public function findFree(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.drivers', 'd')
            ->having('COUNT(d.id) = 0')
            ->groupBy('f.id')
            ->getQuery()
            ->getResult();
    }
}

