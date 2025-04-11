<?php namespace App\Controller;

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

    // Inject CreateCommandeUseCase into the controller constructor
    public function __construct(
        CommandeRepository $commandeRepository,
        VehicleRepository $vehicleRepository
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    // Modified create method to use CreateCommandeUseCase
    #[Route("/commande/create", name:"commande_create", methods:["GET"])]
    public function create(Request $request, CreateCommandeUseCase $createCommandeUseCase): Response
    {
    
        $userId = $this->getUser()->getId(); // Get the authenticated user's ID
        $assurance = false; // Assume the commande is not paid initially (can be changed based on form input)
        $paymentMethod = ""; // You can change this if the form provides a payment date

        try {
            // Call the use case to create the commande
            $commande = $createCommandeUseCase->execute($userId, $assurance, $paymentMethod);
            
            // After successful creation, redirect to the add reservation page
            // Pass the newly created commande's ID to the next route
            return $this->redirectToRoute('commande_add_reservation', ['id' => $commande->getId()]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error creating commande: ' . $e->getMessage());
        }
        

        return $this->render('commande/create.html.twig');
    }

    #[Route('/commande/{id}/add-reservation', name: 'commande_add_reservation', methods: ['GET', 'POST'])]
    public function addReservation(int $id, Request $request, AddReservationToCommandeUseCase $addReservation): Response
    {
        $commande = $this->commandeRepository->find($id);

        if (!$commande) {
            return $this->json(['error' => 'Commande not found'], Response::HTTP_NOT_FOUND);
        }

        // Fetch vehicles for the form
        $vehicles = $this->vehicleRepository->findAll();

        if ($request->getMethod() === 'POST') {
            $vehicleId = $request->request->get('vehicleId');
            $startDate = new \DateTime($request->request->get('startDate'));
            $endDate = new \DateTime($request->request->get('endDate'));

            try {
                // Add reservation to the commande using the use case
                $addReservation->execute($this->getUser()->getId(), $commande, $vehicleId, $startDate, $endDate);
                return $this->redirectToRoute('commande_view', ['id' => $commande->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error adding reservation: ' . $e->getMessage());
            }
        }

        return $this->render('commande/add_reservation.html.twig', [
            'commande' => $commande,
            'vehicles' => $vehicles,
        ]);
    }

    #[Route('/commande/{id}', name: 'commande_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        $commande = $this->commandeRepository->find($id);
        $vehicles = $this->vehicleRepository->findAll(); // 👈 Fetch the vehicles

        if (!$commande) {
            return $this->json(['error' => 'Commande not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->render('commande/view.html.twig', [
            'commande' => $commande,
            'vehicles' => $vehicles

        ]);
    }

    #[Route('/commande/{id}/checkout', name: 'commande_checkout', methods: ['GET', 'POST'])]
    public function checkout(int $id, Request $request, UpdateAndPayCommandeUseCase $updateAndPayUseCase): Response
    {
        $commande = $this->commandeRepository->find($id);
        if (!$commande) {
            throw $this->createNotFoundException('Commande not found');
        }
    
        $total = 0;
        foreach ($commande->getReservations() as $reservation) {
            $total += $reservation->getTotalPrice();
        }
    
        if ($request->isMethod('POST')) {
            $assurance = $request->request->get('assurance') === 'on';
            $paymentMethod = $request->request->get('payment_method');
    
            try {
                $updateAndPayUseCase->execute($id, $commande, $assurance, $paymentMethod);
                $this->addFlash('success', 'Commande paid successfully!');
                return $this->redirectToRoute('commande_view', ['id' => $commande->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error: ' . $e->getMessage());
            }
        }
    
        return $this->render('commande/checkout.html.twig', [
            'commande' => $commande,
            'total' => $total,
        ]);
    }
    

}
