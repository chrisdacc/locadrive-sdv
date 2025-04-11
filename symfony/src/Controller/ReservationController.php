<?php
namespace App\Controller;

use App\Application\CreateReservationUseCase;
use App\Application\DeleteReservationUseCase;
use App\Application\GetReservationByIdUseCase;
use App\Application\ListReservationsUseCase;
use App\Application\UpdateReservationUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;

class ReservationController extends AbstractController
{
    private $createReservationUseCase;
    private $listReservationsUseCase;
    private $getReservationByIdUseCase;
    private $deleteReservationUseCase;
    private $updateReservationUseCase;
    private $userRepository;
    private $vehicleRepository;


    public function __construct(
        CreateReservationUseCase $createReservationUseCase,
        ListReservationsUseCase $listReservationsUseCase,
        GetReservationByIdUseCase $getReservationByIdUseCase,
        DeleteReservationUseCase $deleteReservationUseCase,
        UpdateReservationUseCase $updateReservationUseCase,
        UserRepository $userRepository,
        VehicleRepository $vehicleRepository
    ) {
        $this->createReservationUseCase = $createReservationUseCase;
        $this->listReservationsUseCase = $listReservationsUseCase;
        $this->getReservationByIdUseCase = $getReservationByIdUseCase;
        $this->deleteReservationUseCase = $deleteReservationUseCase;
        $this->updateReservationUseCase = $updateReservationUseCase;
        $this->userRepository = $userRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    #[Route('/create-reservation', name: 'create_reservation', methods: ['GET', 'POST'])]
    public function createReservation(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            $vehicleId = (int)$request->request->get('vehicle_id');
            $userId = (int)$request->request->get('user_id');
            $startDate = new \DateTime($request->request->get('start_date'));
            $endDate = new \DateTime($request->request->get('end_date'));

            if ($userId == null) {
                $userId = $this->getUser()->getId();
            }

            if (!$vehicleId || !$userId || !$startDate || !$endDate) {
                $this->addFlash("error", "All fields must be filled out.");
                return $this->render('reservation/create.html.twig', [
                    'users' => $this->userRepository->findAll(),
                    'vehicles' => $this->vehicleRepository->findAll(),
                ]);
            }

            $vehicle = $this->vehicleRepository->find($vehicleId);
            if (!$vehicle) {
                $this->addFlash("error", "Vehicle not found.");
                return $this->render('reservation/create.html.twig', [
                    'users' => $this->userRepository->findAll(),
                    'vehicles' => $this->vehicleRepository->findAll(),
                ]);
            }

            $days = $startDate->diff($endDate)->days;
            $totalPrice = ($days + 1) * $vehicle->getDailyRate();

            try {
                // Create reservation
                $reservation = $this->createReservationUseCase->execute(
                    $userId,
                    $vehicleId,
                    $startDate,
                    $endDate,
                    $totalPrice,
                    null // Assuming null for the commande at this point, will be handled later
                );

                // Now add the reservation to a commande
                // (Assuming the user already has a commande in "CART" status or create a new one)
                $commande = $this->commandeRepository->findActiveCommandeByUser($userId); // Implement method to fetch active commande for the user
                if ($commande) {
                    // Add vehicle to the existing commande (cart)
                    $this->addVehicleToCommandeUseCase->execute($userId, $commande, $vehicleId, $startDate, $endDate);
                    $this->addFlash("success", "Vehicle added to your cart.");
                } else {
                    $this->addFlash("error", "No active cart found.");
                }

            } catch (\Exception $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }

        return $this->render('reservation/create.html.twig', [
            'users' => $this->userRepository->findAll(),
            'vehicles' => $this->vehicleRepository->findAll(),
        ]);
    }


    #[Route('/list-reservations', name: 'list_reservations', methods: ['GET'])]
    public function listReservations(): Response
    {
        $reservations = $this->listReservationsUseCase->execute();
        
        return $this->render('reservation/list.html.twig', [
            'reservations' => $reservations,
            'users' => $this->userRepository->findAll(),
            'vehicles' => $this->vehicleRepository->findAll(),

        ]);
    }

    #[Route('/get-reservation/{id}', name: 'get_reservation_by_id', methods: ['GET'])]
    public function getReservationById(int $id): Response
    {
        try {
            $reservation = $this->getReservationByIdUseCase->execute($id);
        } catch (\Exception $e) {
            return $this->render('404.html.twig');
        }

        return $this->render('reservation/show-reservation.html.twig', [
            'reservation' => $reservation
        ]);
    }

    #[Route('/delete-reservation/{id}', name: 'delete_reservation', methods: ['POST'])]
    public function removeReservation(int $id): Response
    {
        try {
            $this->deleteReservationUseCase->execute($id);
        } catch (\Exception $e) {
            return $this->render('404.html.twig');
        }

        $this->addFlash("success", "Reservation deleted.");
        return $this->redirectToRoute('list_reservations');
    }

    #[Route('/update-reservation/{id}', name: 'update_reservation', methods: ['GET', 'POST'])]
    public function updateReservation(int $id, Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            $vehicleId = (int)$request->request->get('vehicle_id');
            $userId = (int)$request->request->get('user_id');
            $startDate = new \DateTime($request->request->get('start_date'));
            $endDate = new \DateTime($request->request->get('end_date'));

            if (!$vehicleId || !$userId || !$startDate || !$endDate) {
                $this->addFlash("error", "All fields must be filled out.");
            }
            $vehicle = $this->vehicleRepository->find($vehicleId);
            $days = $startDate->diff($endDate)->days;
            $totalPrice = $days * $vehicle->getDailyRate();
            try {
                $this->updateReservationUseCase->execute($id, $vehicleId, $startDate, $endDate, $totalPrice);
                
                $this->addFlash("success", "Reservation updated.");

                return $this->redirectToRoute('list_reservations');
            } catch (\Exception $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }

        try {
            $reservation = $this->getReservationByIdUseCase->execute($id);
        } catch (\Exception $e) {
            return $this->render('404.html.twig');
        }

        return $this->render('reservation/update-reservation.html.twig', [
            'reservation' => $reservation
        ]);
    }
}
