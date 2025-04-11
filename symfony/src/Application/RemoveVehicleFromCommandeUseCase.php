<?php 
namespace App\UseCase;

use App\Entity\Commande;
use App\Repository\CommandeRepository;

class RemoveVehicleFromCommandeUseCase
{
    private CommandeRepository $commandeRepository;

    public function __construct(CommandeRepository $commandeRepository)
    {
        $this->commandeRepository = $commandeRepository;
    }

    public function execute(Commande $commande, int $reservationId): Commande
    {
        // Ensure the commande is in "CART" status
        if ($commande->getStatut() !== 'CART') {
            throw new \LogicException('You can only remove vehicles from a cart that is still open.');
        }

        // Remove the reservation from the commande
        $reservation = $commande->getReservations()->filter(function ($r) use ($reservationId) {
            return $r->getId() === $reservationId;
        })->first();

        if ($reservation) {
            $commande->removeReservation($reservation);
            $this->commandeRepository->save($commande);
        } else {
            throw new \Exception('Reservation not found.');
        }

        return $commande;
    }
}
