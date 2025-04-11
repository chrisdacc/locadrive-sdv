<?php

namespace App\Controller;

use App\Application\CreateCommandeUseCase;
use App\Application\UpdateAndPayCommandeUseCase;
use App\UseCase\AddReservationToCommandeUseCase;
use App\Repository\CommandeRepository;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    private CommandeRepository $commandeRepository;
    private VehicleRepository $vehicleRepository;

    public function __construct(
        CommandeRepository $commandeRepository,
        VehicleRepository $vehicleRepository
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    #[Route("/commande/create", name:"commande_create", methods:["POST"])]
    public function create(Request $request, CreateCommandeUseCase $createCommandeUseCase): Response
    {
        $userId = $this->getUser()->getId();
        $assurance = $request->get('assurance', false);
        $paymentMethod = $request->get('payment_method', '');

        try {
            $commande = $createCommandeUseCase->execute($userId, $assurance, $paymentMethod);
            return $this->json([
                'message' => 'Commande created successfully',
                'commandeId' => $commande->getId()
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/commande/{id}/add-reservation', name: 'commande_add_reservation', methods: ['POST'])]
    public function addReservation(int $id, Request $request, AddReservationToCommandeUseCase $addReservation): Response
    {
        $commande = $this->commandeRepository->find($id);

        if (!$commande) {
            return $this->json(['error' => 'Commande not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $vehicleId = $request->get('vehicleId');
            $startDate = new \DateTime($request->get('startDate'));
            $endDate = new \DateTime($request->get('endDate'));

            $addReservation->execute($this->getUser()->getId(), $commande, $vehicleId, $startDate, $endDate);

            return $this->json(['message' => 'Reservation added successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/commande/{id}', name: 'commande_view', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function view(int $id): Response
    {
        $commande = $this->commandeRepository->find($id);
        if (!$commande) {
            return $this->json(['error' => 'Commande not found'], Response::HTTP_NOT_FOUND);
        }

        $reservations = [];
        foreach ($commande->getReservations() as $res) {
            $reservations[] = [
                'vehicle' => $res->getVehicle()->getModel(),
                'startDate' => $res->getStartDate()->format('Y-m-d'),
                'endDate' => $res->getEndDate()->format('Y-m-d'),
                'price' => $res->getTotalPrice()
            ];
        }

        return $this->json([
            'commandeId' => $commande->getId(),
            'assurance' => $commande->hasAssurance(),
            'paymentMethod' => $commande->getPaymentMethod(),
            'reservations' => $reservations,
        ]);
    }

    #[Route('/commande/{id}/checkout', name: 'commande_checkout', methods: ['POST'])]
    public function checkout(int $id, Request $request, UpdateAndPayCommandeUseCase $updateAndPayUseCase): Response
    {
        $commande = $this->commandeRepository->find($id);
        if (!$commande) {
            return $this->json(['error' => 'Commande not found'], Response::HTTP_NOT_FOUND);
        }

        $assurance = $request->get('assurance', false);
        $paymentMethod = $request->get('payment_method');

        try {
            $updateAndPayUseCase->execute($id, $commande, $assurance, $paymentMethod);
            return $this->json(['message' => 'Commande paid successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
