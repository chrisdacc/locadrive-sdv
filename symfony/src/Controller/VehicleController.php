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
    private $createVehicleUseCase;
    private $listVehiclesUseCase;
    private $getVehicleByIdUseCase;
    private $deleteVehicleUseCase;
    private $updateVehicleUseCase;

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

    #[Route('/admin/create-vehicle', name: 'create_vehicle', methods: ['GET', 'POST'])]
    public function createVehicle(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            $model = $request->request->get('model');
            $brand = $request->request->get('brand');
            $price = (float)$request->request->get('dailyRate');
            $user = $this->getUser();

            if (!$model || !$brand || !$price) {
                $this->addFlash("error", "Model, brand, and price must be provided.");
                return $this->render('vehicle/create.html.twig');
            }

            try {
                $this->createVehicleUseCase->execute($model, $brand, $price);
                $this->addFlash("success", "Vehicle created.");
            } catch (\Exception $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }

        return $this->render('vehicle/create.html.twig');
    }

    #[Route('/list-vehicles', name: 'list_vehicles', methods: ['GET'])]
    public function listVehicles(): Response
    {
        $vehicles = $this->listVehiclesUseCase->execute();

        return $this->render('vehicle/list.html.twig', [
            'vehicles' => $vehicles
        ]);
    }

    #[Route('/get-vehicle/{id}', name: 'get_vehicle_by_id', methods: ['GET'])]
    public function getVehicleById(int $id): Response
    {
        try {
            $vehicle = $this->getVehicleByIdUseCase->execute($id);
        } catch (\Exception $e) {
            return $this->render('404.html.twig');
        }

        return $this->render('vehicle/show-vehicle.html.twig', [
            'vehicle' => $vehicle
        ]);
    }

    #[Route('/admin/delete-vehicle/{id}', name: 'delete_vehicle', methods: ['POST'])]
    public function removeVehicle(int $id): Response
    {
        try {
            $this->deleteVehicleUseCase->execute($id);
        } catch (\Exception $e) {
            return $this->render('404.html.twig');
        }

        $this->addFlash("success", "Vehicle deleted.");
        return $this->redirectToRoute('list_vehicles');
    }

    #[Route('/admin/update-vehicle/{id}', name: 'update_vehicle', methods: ['GET', 'POST'])]
    public function updateVehicle(int $id, Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            $model = $request->request->get('model');
            $brand = $request->request->get('brand');
            $price = (float)$request->request->get('dailyRate');

            if (!$model || !$brand || !$price) {
                $this->addFlash("error", "Model, brand, and price must be provided.");
            }

            try {
                $this->updateVehicleUseCase->execute($id, $model, $brand, $price);
                $this->addFlash("success", "Vehicle updated.");

                return $this->redirectToRoute('list_vehicles');
            } catch (\Exception $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }

        try {
            $vehicle = $this->getVehicleByIdUseCase->execute($id);
        } catch (\Exception $e) {
            return $this->render('404.html.twig');
        }

        return $this->render('vehicle/update-vehicle.html.twig', [
            'vehicle' => $vehicle
        ]);
    }
}
