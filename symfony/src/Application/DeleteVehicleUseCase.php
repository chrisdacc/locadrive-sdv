<?php

namespace App\Application;

use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Vehicle;

class DeleteVehicleUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(int $id): void
    {
        $vehicle = $this->entityManager->getRepository(Vehicle::class)->find($id);

        if (!$vehicle) {
            throw new \Exception("Vehicle not found.");
        }

        try {
            $this->entityManager->remove($vehicle);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception("Cannot delete vehicle. Please try again later.");
        }
    }
}
