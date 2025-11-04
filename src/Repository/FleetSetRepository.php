<?php

namespace App\Repository;

use App\Entity\FleetSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @throws NotFoundHttpException
     */
    public function findOrFail(string $id): FleetSet
    {
        $fleetSet = $this->find($id);

        if (!$fleetSet) {
            throw new NotFoundHttpException('FleetSet not found');
        }

        return $fleetSet;
    }

    /**
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
     * @return array{total: int, works: int, free: int, downtime: int}
     */
    public function getFleetStatistics(): array
    {
        $qb = $this->createQueryBuilder('f')
            ->select('COUNT(f.id) as total')
            ->leftJoin('f.truck', 't')
            ->leftJoin('f.trailer', 'tr')
            ->leftJoin('f.drivers', 'd')
            ->leftJoin('App\Entity\Order', 'o', 'WITH', 'o.fleetSet = f.id AND o.status IN (:activeStatuses)')
            ->setParameter('activeStatuses', ['pending', 'in_progress']);

        $qb->addSelect(
            "SUM(CASE
                WHEN t.status = 'in_service' OR tr.status = 'in_service' THEN 1
                WHEN o.id IS NOT NULL THEN 1
                ELSE 0
            END) as downtime"
        );

        $qb->addSelect(
            "SUM(CASE
                WHEN (t.status = 'operational' AND tr.status = 'operational' AND o.id IS NULL AND SIZE(f.drivers) > 0) THEN 1
                ELSE 0
            END) as works"
        );

        $qb->addSelect(
            "SUM(CASE
                WHEN (t.status = 'operational' AND tr.status = 'operational' AND o.id IS NULL AND SIZE(f.drivers) = 0) THEN 1
                ELSE 0
            END) as free"
        );

        $result = $qb->getQuery()->getSingleResult();

        return [
            'total' => (int) $result['total'],
            'works' => (int) ($result['works'] ?? 0),
            'free' => (int) ($result['free'] ?? 0),
            'downtime' => (int) ($result['downtime'] ?? 0),
        ];
    }
}
