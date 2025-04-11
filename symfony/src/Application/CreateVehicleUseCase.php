<?php

namespace App\Application;

use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;

class CreateVehicleUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(string $model, string $brand, float $dailyRate): void
    {
        try {
            $vehicle = new Vehicle($model, $brand,  $dailyRate);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        try {
            $this->entityManager->persist($vehicle);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new \Exception("Cannot create vehicle. Please try again later.");
        }
    }
}
