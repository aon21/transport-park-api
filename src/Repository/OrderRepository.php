<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Save an order entity
     */
    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove an order entity
     */
    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find all active orders (status: pending or in_progress)
     * 
     * @return Order[]
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.status IN (:statuses)')
            ->setParameter('statuses', ['pending', 'in_progress'])
            ->orderBy('o.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all orders with their relationships loaded (truck, trailer, fleetSet)
     * 
     * @return Order[]
     */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.truck', 't')
            ->leftJoin('o.trailer', 'tr')
            ->leftJoin('o.fleetSet', 'f')
            ->addSelect('t', 'tr', 'f')
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find active orders for a specific truck
     * 
     * @return Order[]
     */
    public function findActiveForTruck(Uuid $truckId): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.truck = :truckId')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('truckId', $truckId, 'uuid')
            ->setParameter('statuses', ['pending', 'in_progress'])
            ->getQuery()
            ->getResult();
    }

    /**
     * Find active orders for a specific trailer
     * 
     * @return Order[]
     */
    public function findActiveForTrailer(Uuid $trailerId): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.trailer = :trailerId')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('trailerId', $trailerId, 'uuid')
            ->setParameter('statuses', ['pending', 'in_progress'])
            ->getQuery()
            ->getResult();
    }

    /**
     * Find active orders for a specific fleet set
     * 
     * @return Order[]
     */
    public function findActiveForFleetSet(Uuid $fleetSetId): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.fleetSet = :fleetSetId')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('fleetSetId', $fleetSetId, 'uuid')
            ->setParameter('statuses', ['pending', 'in_progress'])
            ->getQuery()
            ->getResult();
    }

    /**
     * Find orders by status
     * 
     * @return Order[]
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find orders by service type
     * 
     * @return Order[]
     */
    public function findByServiceType(string $serviceType): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.serviceType = :serviceType')
            ->setParameter('serviceType', $serviceType)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

