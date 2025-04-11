<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class LoginController extends AbstractController
{
    #[Route('api/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // This endpoint is handled by the Symfony security system.
        // This method will only be called if login fails.

        return $this->json(['error' => 'Invalid credentials.'], 401);
    }
}
