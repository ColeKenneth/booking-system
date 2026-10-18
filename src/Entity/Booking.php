<?php

namespace App\Entity;

use App\Enum\BookingStatus;
use App\Repository\BookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private Passenger $passenger;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Driver $driver = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message: "Pickup location is required.")]
    #[Assert\Length(max: 200)]
    private string $pickupLocation;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message: "Drop off location is required.")]
    #[Assert\Length(max: 200)]
    private string $dropOffLocation;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Fare amount is required.")]
    #[Assert\Regex(pattern: '/^\d{1,8}(\.\d{1,2})?$/', message: 'Fare must be a valid non-negative amount with up to two decimal places.')]
    private string $fare;

    #[ORM\Column(enumType: BookingStatus::class, nullable: true)]
    private ?BookingStatus $bookingStatus = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(Passenger $passenger)
    {
        $this->passenger = $passenger;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassenger(): Passenger
    {
        return $this->passenger;
    }

    public function setPassenger(Passenger $passenger): static
    {
        $this->passenger = $passenger;

        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    public function getPickupLocation(): string
    {
        return $this->pickupLocation;
    }

    public function setPickupLocation(string $pickupLocation): static
    {
        $this->pickupLocation = $pickupLocation;

        return $this;
    }

    public function getDropOffLocation(): string
    {
        return $this->dropOffLocation;
    }

    public function setDropOffLocation(string $dropOffLocation): static
    {
        $this->dropOffLocation = $dropOffLocation;

        return $this;
    }

    public function getFare(): string
    {
        return $this->fare;
    }

    public function setFare(string $fare): static
    {
        $this->fare = $fare;

        return $this;
    }

    public function getBookingStatus(): ?BookingStatus
    {
        return $this->bookingStatus;
    }

    public function setBookingStatus(?BookingStatus $bookingStatus): static
    {
        $this->bookingStatus = $bookingStatus;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
