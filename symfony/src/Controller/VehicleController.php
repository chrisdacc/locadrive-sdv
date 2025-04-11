<?php

namespace App\Controller;

use App\Application\CreateVehicleUseCase;
use App\Application\DeleteVehicleUseCase;
use App\Application\GetVehicleByIdUseCase;
use App\Application\ListVehiclesUseCase;
use App\Application\UpdateVehicleUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends AbstractController
{
    private CreateVehicleUseCase $createVehicleUseCase;
    private ListVehiclesUseCase $listVehiclesUseCase;
    private GetVehicleByIdUseCase $getVehicleByIdUseCase;
    private DeleteVehicleUseCase $deleteVehicleUseCase;
    private UpdateVehicleUseCase $updateVehicleUseCase;

    public function __construct(
        CreateVehicleUseCase $createVehicleUseCase,
        ListVehiclesUseCase $listVehiclesUseCase,
        GetVehicleByIdUseCase $getVehicleByIdUseCase,
        DeleteVehicleUseCase $deleteVehicleUseCase,
        UpdateVehicleUseCase $updateVehicleUseCase
    ) {
        $this->createVehicleUseCase = $createVehicleUseCase;
        $this->listVehiclesUseCase = $listVehiclesUseCase;
        $this->getVehicleByIdUseCase = $getVehicleByIdUseCase;
        $this->deleteVehicleUseCase = $deleteVehicleUseCase;
        $this->updateVehicleUseCase = $updateVehicleUseCase;
    }

    #[Route('/api/vehicles', name: 'api_create_vehicle', methods: ['POST'])]
    public function createVehicle(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $model = $data['model'] ?? null;
        $brand = $data['brand'] ?? null;
        $price = isset($data['dailyRate']) ? (float) $data['dailyRate'] : null;

        if (!$model || !$brand || !$price) {
            return $this->json(['error' => 'Model, brand, and daily rate are required.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $vehicle = $this->createVehicleUseCase->execute($model, $brand, $price);
            return $this->json(['message' => 'Vehicle created', 'vehicle' => $vehicle], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/vehicles', name: 'api_list_vehicles', methods: ['GET'])]
    public function listVehicles(): Response
    {
        $vehicles = $this->listVehiclesUseCase->execute();
        return $this->json($vehicles);
    }

    #[Route('/api/vehicles/{id}', name: 'api_get_vehicle_by_id', methods: ['GET'])]
    public function getVehicleById(int $id): Response
    {
        try {
            $vehicle = $this->getVehicleByIdUseCase->execute($id);
            return $this->json($vehicle);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Vehicle not found.'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/vehicles/{id}', name: 'api_delete_vehicle', methods: ['DELETE'])]
    public function removeVehicle(int $id): Response
    {
        try {
            $this->deleteVehicleUseCase->execute($id);
            return $this->json(['message' => 'Vehicle deleted']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Vehicle not found.'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/vehicles/{id}', name: 'api_update_vehicle', methods: ['PUT'])]
    public function updateVehicle(int $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $model = $data['model'] ?? null;
        $brand = $data['brand'] ?? null;
        $price = isset($data['dailyRate']) ? (float) $data['dailyRate'] : null;

        if (!$model || !$brand || !$price) {
            return $this->json(['error' => 'Model, brand, and daily rate are required.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $vehicle = $this->updateVehicleUseCase->execute($id, $model, $brand, $price);
            return $this->json(['message' => 'Vehicle updated', 'vehicle' => $vehicle]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
