<?php

namespace App\Application;

use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;

class GetVehicleByIdUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(int $id): ?Vehicle
    {
        $vehicle = $this->entityManager->getRepository(Vehicle::class)->find($id);

        if (!$vehicle) {
            throw new \Exception("Vehicle not found.");
        }

        return $vehicle;
    }
}
