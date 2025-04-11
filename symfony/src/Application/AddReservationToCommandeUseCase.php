<?php

namespace App\UseCase;

use App\Application\CreateReservationUseCase;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\VehicleRepository;

class AddReservationToCommandeUseCase
{
    private CommandeRepository $commandeRepository;
    private VehicleRepository $vehicleRepository;
    private CreateReservationUseCase $createReservationUseCase;

    public function __construct(
        CommandeRepository $commandeRepository,
        VehicleRepository $vehicleRepository,
        CreateReservationUseCase $createReservationUseCase
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->createReservationUseCase = $createReservationUseCase;
    }

    public function execute(
        int $userId,
        Commande $commande,
        int $vehicleId,
        \DateTime $startDate,
        \DateTime $endDate
    ): Commande {
        if ($commande->getStatut() !== 'CART') {
            throw new \LogicException('You can only add reservations to a cart that is still open.');
        }

        $vehicle = $this->vehicleRepository->find($vehicleId);
        if (!$vehicle) {
            throw new \LogicException('The selected vehicle does not exist.');
        }

        $reservation = $this->createReservationUseCase->execute(
            $userId,
            $vehicleId,
            $startDate,
            $endDate,
            $vehicle->getDailyRate(),
            $commande
        );

        $commande->addReservation($reservation);
        $this->commandeRepository->save($commande);

        return $commande;
    }
}
