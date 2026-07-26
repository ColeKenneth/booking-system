<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Services;

use OnlineBooking\src\Exceptions\DriverAlreadyExistsException;
use OnlineBooking\src\Models\Driver;
use OnlineBooking\src\Models\DriverStatus;
use OnlineBooking\src\Repository\DriverRepository;

final readonly class DriverService
{
    public function __construct(private DriverRepository $driverRepository, private UserService $userService){}

    public function registerDriver(int $userId, string $carBrand, string $plate_number, DriverStatus $driverStatus = DriverStatus::OFFLINE) : Driver
    {
        $user = $this->userService->getUserById($userId);

        if ($this->driverRepository->findDriverById($userId) !== null) {
            throw new DriverAlreadyExistsException("Driver already exists.", code: 409);
        }

        $driver = new Driver(
            driverId: null,
            userId: $userId,
            fullName: $user->fullName,
            username: $user->username,
            password: $user->getHashedPassword(),
            carBrand: $carBrand,
            plateNumber: $plate_number,
            driverStatus: $driverStatus
        );

        $this->driverRepository->saveDriver($driver);

        return $driver;

    }
}