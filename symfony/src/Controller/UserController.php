<?php

namespace App\Controller;

use App\Application\CreateUserUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private CreateUserUseCase $createUserUseCase;

    public function __construct(CreateUserUseCase $createUserUseCase) {
        $this->createUserUseCase = $createUserUseCase;
    }

    #[Route("/api/register", name: "api_register", methods: ["POST"])]
    public function createUser(Request $request): Response {
        $email = $request->get('email');
        $password = $request->get('password');
        $role = "ROLE_USER";

        if (!$email || !$password) {
            return $this->json([
                'error' => 'Email and password are required.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->createUserUseCase->execute($email, $password, $role);

            return $this->json([
                'message' => 'User created successfully.',
                'email' => $email
            ], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return $this->json([
                'error' => $exception->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
