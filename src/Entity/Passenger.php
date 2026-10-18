<?php
declare(strict_types = 1);

namespace App\Entity;

use App\Enum\PassengerStatus;
use App\Repository\PassengerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PassengerRepository::class)]
class Passenger
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'passengers')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(name: "passenger_status", enumType: PassengerStatus::class)]
    private PassengerStatus $passengerStatus = PassengerStatus::IDLE;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'passenger')]
    private Collection $bookings;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->createdAt = new \DateTimeImmutable();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPassengerStatus(): PassengerStatus
    {
        return $this->passengerStatus;
    }

    public function setPassengerStatus(PassengerStatus $passengerStatus): static
    {
        $this->passengerStatus = $passengerStatus;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
        }

        if ($booking->getPassenger() !== $this) {
            $booking->setPassenger($this);
        }

        return $this;
    }
}
