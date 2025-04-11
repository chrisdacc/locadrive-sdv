<?php

namespace App\Application;

use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservation;

class DeleteReservationUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(int $id): void
    {
        $reservation = $this->entityManager->getRepository(Reservation::class)->find($id);

        if (!$reservation) {
            throw new \Exception("Reservation not found.");
        }

        try {
            $this->entityManager->remove($reservation);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Cannot delete reservation. Please try again later.");
        }
    }
}
