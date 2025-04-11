<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('api/login', name: 'login', methods: ['POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        if ($error) {
            return new JsonResponse([
                'success' => false,
                'message' => $error->getMessageKey()
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getUserIdentifier(),
                'roles' => $user->getRoles()
            ]
        ]);
    }

    #[Route("/logout", name: "logout", methods: ["POST"])]
    public function logout(): void
    {
        throw new \LogicException('This method is intercepted by Symfony firewall during logout.');
    }
}
