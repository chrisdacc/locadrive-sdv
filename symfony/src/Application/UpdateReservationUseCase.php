<?php

namespace App\Application;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

class UpdateReservationUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(int $id, string $vehicle, \DateTime $startDate, \DateTime $endDate, float $price): void
    {
        $reservation = $this->entityManager->getRepository(Reservation::class)->find($id);

        if (!$reservation) {
            throw new \Exception("Reservation not found.");
        }

        try {
            $reservation->setVehicleId($vehicle);
            $reservation->setStartDate($startDate);
            $reservation->setEndDate($endDate);
            $reservation->setTotalPrice($price);

            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Cannot update reservation. Please try again later.");
        }
    }
}
