<?php
namespace App\UseCase;

use App\Entity\Commande;
use App\Repository\CommandeRepository;

class RemoveInsuranceFromCommandeUseCase
{
    private CommandeRepository $commandeRepository;

    public function __construct(CommandeRepository $commandeRepository)
    {
        $this->commandeRepository = $commandeRepository;
    }

    public function execute(Commande $commande): Commande
    {
        // Ensure the commande is in "CART" status
        if ($commande->getStatut() !== 'CART') {
            throw new \LogicException('You can only remove insurance from a cart that is still open.');
        }

        // Remove insurance
        $commande->setAssurance(null);
        $this->commandeRepository->save($commande);

        return $commande;
    }
}
