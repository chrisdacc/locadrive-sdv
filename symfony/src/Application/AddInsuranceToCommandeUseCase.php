<?php
namespace App\UseCase;

use App\Entity\Commande;
use App\Service\InsuranceService;
use App\Repository\CommandeRepository;

class AddInsuranceToCommandeUseCase
{
    private CommandeRepository $commandeRepository;
    private InsuranceService $insuranceService;

    public function __construct(CommandeRepository $commandeRepository, InsuranceService $insuranceService)
    {
        $this->commandeRepository = $commandeRepository;
        $this->insuranceService = $insuranceService;
    }

    public function execute(Commande $commande): Commande
    {
        // Ensure the commande is in "CART" status
        if ($commande->getStatut() !== 'CART') {
            throw new \LogicException('You can only add insurance to a cart that is still open.');
        }

        // Add insurance
        $insurance = $this->insuranceService->getInsurance();
        $commande->setAssurance($insurance);
        $this->commandeRepository->save($commande);

        return $commande;
    }
}
