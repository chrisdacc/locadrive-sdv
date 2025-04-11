<?php

namespace App\Application;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserUseCase
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ) {
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
    }

    public function execute(string $email, string $password, string $role): void
    {
        $user = new User($email, $password, $role, $this->hasher);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new \Exception('Cannot create user.');
        }
    }
}
