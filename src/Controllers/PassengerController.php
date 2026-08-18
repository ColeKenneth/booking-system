<?php
declare(strict_types=1);

namespace OnlineBooking\src\Controllers;

use OnlineBooking\src\DTOs\RegisterPassengerCommand;
use OnlineBooking\src\Models\PassengerStatus;
use OnlineBooking\src\Services\PassengerService;

final readonly class PassengerController
{
    public function __construct(
        private PassengerService $passengerService
    ) {}

    public function register(RegisterPassengerCommand $command)
    {
        return $this->passengerService->registerPassenger($command);
    }

    public function getById(int $passengerId)
    {
        return $this->passengerService->getPassengerById($passengerId);
    }

    public function getByUserId(int $userId)
    {
        return $this->passengerService->getPassengerByUserId($userId);
    }

    public function updateStatus(int $passengerId, PassengerStatus $status): void
    {
        $this->passengerService->updateStatus($passengerId, $status);
    }

    public function delete(int $passengerId): void
    {
        $this->passengerService->deletePassenger($passengerId);
    }
}