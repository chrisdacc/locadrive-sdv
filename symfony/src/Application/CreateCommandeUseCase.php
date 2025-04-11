<?php namespace App\Application;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;

class CreateCommandeUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(int $userId, bool $assurance, ?string $paymentMethod): Commande
    {
        try {
            // Create a new Commande entity
            $commande = new Commande($userId, $assurance, $paymentMethod);
            
        } catch (\Exception $e) {
            throw new \Exception("Error creating commande: " . $e->getMessage());
        }

        try {
            // Persist and flush the Commande entity to the database
            $this->entityManager->persist($commande);
            $this->entityManager->flush();
            return $commande;
            
        } catch (\Exception $exception) {
            throw new \Exception("Cannot create commande. Please try again later.");
        }
    }
}
