<?php

namespace App\Repository;

use App\Entity\Driver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Driver>
 */
class DriverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Driver::class);
    }

    public function save(Driver $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Driver $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find drivers by license number pattern
     *
     * @return Driver[]
     */
    public function searchByLicense(string $pattern): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.licenseNumber LIKE :pattern')
            ->setParameter('pattern', '%' . $pattern . '%')
            ->orderBy('d.lastName', 'ASC')
            ->addOrderBy('d.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find drivers by last name
     *
     * @return Driver[]
     */
    public function findByLastName(string $lastName): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.lastName = :lastName')
            ->setParameter('lastName', $lastName)
            ->orderBy('d.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
