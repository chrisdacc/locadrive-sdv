<?php

namespace App\Application;

use App\Entity\Vehicle;
use Doctrine\ORM\EntityManagerInterface;

class ListVehiclesUseCase
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function execute(): array
    {
        return $this->entityManager->getRepository(Vehicle::class)->findAll();
    }
}
