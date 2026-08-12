<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Services;

use OnlineBooking\src\DTOs\DriverResponseDTO;
use OnlineBooking\src\DTOs\RegisterDriverCommand;
use OnlineBooking\src\DTOs\UpdateDriverCommand;
use OnlineBooking\src\Exceptions\DriverAlreadyExistsException;
use OnlineBooking\src\Exceptions\DriverNotFoundException;
use OnlineBooking\src\Models\Driver;
use OnlineBooking\src\Models\DriverStatus;
use OnlineBooking\src\Repository\DriverRepository;

final readonly class DriverService
{
    public function __construct(private DriverRepository $driverRepository, private UserService $userService){}

    public function registerDriver(RegisterDriverCommand $driverCommand) : DriverResponseDTO
    {
        $user = $this->userService->getUserById($driverCommand->userId);

        if ($this->driverRepository->findDriverByUserId($driverCommand->userId) !== null) {
            throw new DriverAlreadyExistsException("Driver already exists.", code: 409);
        }

        $driver = new Driver(
            driverId: null,
            userId: $user->userId,
            fullName: $user->fullName,
            username: $user->username,
            password: $user->getHashedPassword(),
            carBrand: $driverCommand->carBrand,
            plateNumber: $driverCommand->plateNumber,
            driverStatus: $driverCommand->driverStatus
        );

        $this->driverRepository->saveDriver($driver);

        return $this->mapToResponse($driver);
    }

    public function getDriverById(int $driverId) : DriverResponseDTO
    {
        return $this->mapToResponse($this->findDriverById($driverId));
    }

    public function getDriverByUserId(int $userId) : DriverResponseDTO
    {
        return $this->mapToResponse($this->findDriverByUserId($userId));
    }

    public function updateDriverProfile(UpdateDriverCommand $command) : DriverResponseDTO
    {
        $driver = $this->findDriverById($command->driverId);
        $existing = $this->driverRepository->findDriverByPlateNumber($command->plateNumber);

        if ($existing !== null && $existing->driverId !== $command->driverId) {
            throw new DriverAlreadyExistsException("A driver with plate number $command->plateNumber already exists.", code: 409);
        }

        $driver->carBrand = $command->carBrand;
        $driver->plateNumber = $command->plateNumber;

        $driver->validate();
        $this->driverRepository->updateDriver($driver);
        return $this->mapToResponse($driver);
    }

    public function updateStatus(int $driverId, DriverStatus $driverStatus) : void
    {
        $this->findDriverById($driverId);
        $this->driverRepository->updateStatus($driverId, $driverStatus);
    }

    public function deleteDriver(int $driverId) : void 
    {
        $this->driverRepository->findDriverById($driverId);
        $this->driverRepository->deleteDriver($driverId);
    }

    private function findDriverById(int $driverId) : Driver
    {
        return $this->driverRepository->findDriverById($driverId)
        ?? throw new DriverNotFoundException("Driver not found with an ID of $driverId", code: 404);
    }

    private function findDriverByUserId(int $userId) : Driver
    {
        return $this->driverRepository->findDriverByUserId($userId)
        ?? throw new DriverNotFoundException("Driver not found with user ID of $userId", code: 404);
    }

    private function mapToResponse(Driver $driver) : DriverResponseDTO
    {
        return new DriverResponseDTO(
            driverId: $driver->driverId,
            userId: $driver->userId,
            fullName: $driver->fullName,
            username: $driver->username,
            carBrand: $driver->carBrand,
            plateNumber: $driver->plateNumber,
            driverStatus: $driver->driverStatus->value,
            createdAt: $driver->formattedCreatedAt
        );
    }
}