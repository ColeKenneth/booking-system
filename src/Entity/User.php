<?php
declare(strict_types = 1);

namespace App\Entity;

use App\Enum\UserRole;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "first_name", length: 100)]
    #[Assert\NotBlank(message: "First name is required.")]
    #[Assert\Length(min: 3, max: 100)]
    private string $firstName;

    #[ORM\Column(name: "middle_name", length: 100, nullable: true)]
    #[Assert\Length(min: 1, max: 100)]
    private ?string $middleName = null;

    #[ORM\Column(name: "last_name", length: 100)]
    #[Assert\NotBlank(message: "Last name is required.")]
    #[Assert\Length(min: 2, max: 100)]
    private string $lastName;

    #[ORM\Column(name: "username", length: 150, unique: true)]
    #[Assert\NotBlank(message: "Username is required.")]
    #[Assert\Length(min: 3, max: 150)]
    private string $username;

    #[ORM\Column(name: "password", length: 255)]
    #[Assert\NotBlank(message: "Password is required and must be minimum of 8 characters.")]
    #[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', message: "Password must contain an uppercase letter, a lowercase letter, a number, and a special character.")]
    #[Assert\Length(min: 8, max: 255)]
    private string $password;

    #[ORM\Column(enumType: UserRole::class)]
    #[Assert\NotNull(message: "User role is required.")]
    private UserRole $userRole;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, Passenger>
     */
    #[ORM\OneToMany(targetEntity: Passenger::class, mappedBy: 'user')]
    private Collection $passengers;

    /**
     * @var Collection<int, Driver>
     */
    #[ORM\OneToMany(targetEntity: Driver::class, mappedBy: 'user')]
    private Collection $drivers;

    public function __construct()
    {
        $this->passengers = new ArrayCollection();
        $this->drivers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): static
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }


    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getUserRole(): UserRole
    {
        return $this->userRole;
    }

    public function setUserRole(UserRole $userRole): static
    {
        $this->userRole = $userRole;

        return $this;
    }

    /**
     * @return Collection<int, Passenger>
     */
    public function getPassengers(): Collection
    {
        return $this->passengers;
    }

    public function addPassenger(Passenger $passenger): static
    {
        if (!$this->passengers->contains($passenger)) {
            $this->passengers->add($passenger);
        }

        if ($passenger->getUser() !== $this) {
            $passenger->setUser($this);
        }

        return $this;
    }

    public function removePassenger(Passenger $passenger): static
    {
        $this->passengers->removeElement($passenger);

        return $this;
    }

    /**
     * @return Collection<int, Driver>
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): static
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers->add($driver);
        }

        if ($driver->getUser() !== $this) {
            $driver->setUser($this);
        }

        return $this;
    }

    public function removeDriver(Driver $driver): static
    {
        $this->drivers->removeElement($driver);

        return $this;
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    #[Override]
    public function getRoles(): array
    {
        return [$this->userRole->value];
    }

    public function eraseCredentials(): void
    {

    }
}
