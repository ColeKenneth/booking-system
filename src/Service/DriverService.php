<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\Driver;
use App\Entity\User;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;

final readonly class DriverService
{
    public function __construct(
        private DriverRepository $driverRepository,
        private EntityManagerInterface $entityManager
    )
    {}

    public function create(User $user, string $carBrand, string $plateNumber): Driver
    {
        return $this->entityManager->wrapInTransaction(function() use ($user, $carBrand, $plateNumber): Driver {
            $existingDriver = $this->driverRepository->findOneBy(['user' => $user]);

            if ($existingDriver !== null) {
                throw new DomainException("User is already registered as a driver.");
            }

            if ($this->driverRepository->findOneBy(['plateNumber' => $plateNumber]) !== null) {
                throw new DomainException("Plate number is already registered.");
            }

            $driver = new Driver($user);
            $driver->setCarBrand(trim($carBrand));
            $driver->setPlateNumber(strtoupper(trim($plateNumber)));

            $this->entityManager->persist($driver);
            $this->entityManager->flush();

            return $driver;
        });
    }

    public function findById(int $id): ?Driver
    {
        return $this->driverRepository->find($id);
    }

    public function findByUser(User $user): ?Driver
    {
        return $this->driverRepository->findOneBy(['user' => $user]);
    }

    public function updateDriver(Driver $driver, string $carBrand, string $plateNumber): Driver
    {
        return $this->entityManager->wrapInTransaction(function () use ($driver, $carBrand, $plateNumber): Driver {
            $trimmedCarBrand = trim($carBrand);
            $normalizedPlateNumber = strtoupper(trim($plateNumber));

            $existingPlateDriver = $this->driverRepository->findOneBy(['plateNumber' => $normalizedPlateNumber]);
            if ($existingPlateDriver !== null && $existingPlateDriver->getId() !== $driver->getId()) {
                throw new DomainException("Plate number is already registered to another vehicle.");
            }

            $driver->setCarBrand($trimmedCarBrand);
            $driver->setPlateNumber($normalizedPlateNumber);

            $this->entityManager->flush();

            return $driver;
        });
    }
}
