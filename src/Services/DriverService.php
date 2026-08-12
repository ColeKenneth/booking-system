<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Services;

use OnlineBooking\src\DTOs\RegisterDriverCommand;
use OnlineBooking\src\Exceptions\DriverAlreadyExistsException;
use OnlineBooking\src\Exceptions\UserNotFoundException;
use OnlineBooking\src\Models\Driver;
use OnlineBooking\src\Repository\DriverRepository;

final readonly class DriverService
{
    public function __construct(private DriverRepository $driverRepository, private UserService $userService){}

    public function registerDriver(RegisterDriverCommand $driverCommand) : Driver
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

        return $driver;

    }
}