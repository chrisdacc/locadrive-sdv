<?php

namespace App\Application;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ListReservationsUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(): array
    {
        return $this->entityManager->getRepository(Reservation::class)->findAll();
    }
}
