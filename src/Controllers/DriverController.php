<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Controllers;

use OnlineBooking\src\DTOs\DriverResponseDTO;
use OnlineBooking\src\DTOs\RegisterDriverCommand;
use OnlineBooking\src\DTOs\UpdateDriverCommand;
use OnlineBooking\src\Models\DriverStatus;
use OnlineBooking\src\Services\DriverService;

final readonly class DriverController
{
    public function __construct(private DriverService $driverService)
    {}

    public function register(RegisterDriverCommand $command) : DriverResponseDTO
    {
        return $this->driverService->registerDriver($command);
    }

    public function getById(int $driverId) : DriverResponseDTO
    {
        return $this->driverService->getDriverById($driverId);
    }

    public function getByUserId(int $userId) : DriverResponseDTO
    {
        return $this->driverService->getDriverByUserId($userId);
    }

    public function update(UpdateDriverCommand $command) : DriverResponseDTO
    {
        return $this->driverService->updateDriverProfile($command);
    }

    public function updateByStatus(int $driverId, DriverStatus $status) : void
    {
        $this->driverService->updateStatus($driverId, $status);
    }

    public function delete(int $driverId) : void
    {
        $this->driverService->deleteDriver($driverId);
    }
}