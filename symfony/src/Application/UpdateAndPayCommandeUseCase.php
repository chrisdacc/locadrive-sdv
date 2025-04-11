<?php

namespace App\Application;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;


class UpdateAndPayCommandeUseCase
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(int $id, Commande $commande, bool $assurance, string $paymentMethod): void
    {
        $total = 0;
        $commande = $this->entityManager->getRepository(Commande::class)->find($id);

        foreach ($commande->getReservations() as $reservation) {
            $total += $reservation->getTotalPrice();
        }

        if ($assurance) {
            $total += 20;
        }
    try {

        $commande->setAssurance($assurance);
        $commande->setPaymentMehod($paymentMethod);
        $commande->setStatut('PAID');

        $this->entityManager->flush();

    } catch (\Exception $e) {
        throw new \Exception("Cannot update vehicle. Please try again later.");
    }    }
}
