<?php

namespace App\Repository;

use App\Entity\Vehicle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    /**
     * Find all vehicles of a specific type.
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find vehicle by its license plate.
     */
    public function findByLicensePlate(string $licensePlate): ?Vehicle
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.licensePlate = :licensePlate')
            ->setParameter('licensePlate', $licensePlate)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
