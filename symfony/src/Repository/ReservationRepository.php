<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Find all reservations for a specific user.
     */
    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find reservation by its ID.
     */
    public function findById(int $reservationId): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.id = :reservationId')
            ->setParameter('reservationId', $reservationId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all reservations for a specific vehicle.
     */
    public function findByVehicleId(int $vehicleId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.vehicle = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->getQuery()
            ->getResult();
    }
}
