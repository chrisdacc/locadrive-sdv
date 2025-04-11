<?php

namespace App\Application;

use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;

class UpdateVehicleUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(int $id, string $model, string $brand, float $dailyRate): Vehicle
    {
        $vehicle = $this->entityManager->getRepository(Vehicle::class)->find($id);

        if (!$vehicle) {
            throw new \Exception("Vehicle not found.");
        }

        try {
            $vehicle->setModel($model);
            $vehicle->setBrand($brand);
            $vehicle->setDailyRate($dailyRate);

            $this->entityManager->flush();
            return $vehicle;
        } catch (\Exception $e) {
            throw new \Exception("Cannot update vehicle. Please try again later.");
        }
    }
}
