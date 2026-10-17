<?php
declare(strict_types = 1);

namespace App\Entity;

use App\Enum\DriverStatus;
use App\Repository\DriverRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'drivers')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Car brand is required.")]
    #[Assert\Length(max: 50)]
    private string $carBrand;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: "Plate number is required.")]
    #[Assert\Regex(pattern: '/^[A-Z]{3}\s\d{3,4}$/', message: "Invalid plate number format. Sample format: 'ABC 123'")]
    private string $plateNumber;

    #[ORM\Column(enumType: DriverStatus::class)]
    private DriverStatus $driverStatus = DriverStatus::IDLE;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->createdAt = new DateTimeImmutable();
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

    public function getCarBrand(): string
    {
        return $this->carBrand;
    }

    public function setCarBrand(string $carBrand): static
    {
        $this->carBrand = $carBrand;

        return $this;
    }

    public function getPlateNumber(): string
    {
        return $this->plateNumber;
    }

    public function setPlateNumber(string $plateNumber): static
    {
        $this->plateNumber = $plateNumber;

        return $this;
    }

    public function getDriverStatus(): DriverStatus
    {
        return $this->driverStatus;
    }

    public function setDriverStatus(DriverStatus $driverStatus): static
    {
        $this->driverStatus = $driverStatus;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
