<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $clientId = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(length: 20)]
    private string $statut = ''; // CART, PAID, etc.

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Reservation::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\Column(nullable: true)]
    private ?bool $assurance = false;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $paymentMethod = null;

    public function __construct(int $clientId, bool $assurance, string $paymentMethod)
    {
        $this->clientId = $clientId;
        $this->statut = 'CART';
        $this->assurance = $assurance;
        $this->paymentMethod = $paymentMethod;
        $this->createdAt = new \DateTimeImmutable();
        $this->reservations = new ArrayCollection(); // Initialize reservations here
    }


    // Getters/setters go here (can be auto-generated)
    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setCommande($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);

            // Set the owning side to null
            if ($reservation->getCommande() === $this) {
                $reservation->setCommande(null);
            }
        }

        return $this;
    }

    public function getAssurance(): ?bool
    {
        return $this->assurance;
    }

    public function setAssurance(?bool $assurance): self
    {
        $this->assurance = $assurance;

        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }


    public function setPaymentMehod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

}
