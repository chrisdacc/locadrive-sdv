<?php

namespace App\Application;

use App\Entity\Commande;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class CreateReservationUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(int $userId, int $vehicleId, \DateTime $startDate, \DateTime $endDate, float $price, Commande $commande)
    {
        try {
            $reservation = new Reservation($userId, $vehicleId, $startDate, $endDate, $price, $commande);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        try {
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception("Cannot create reservation. Please try again later.");
        }

        return $reservation;
    }
}
