<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Services;

use OnlineBooking\src\Exceptions\PassengerAlreadyExistsException;
use OnlineBooking\src\Exceptions\PassengerNotFoundException;
use OnlineBooking\src\Models\Passenger;
use OnlineBooking\src\DTOs\PassengerResponseDTO;
use OnlineBooking\src\DTOs\RegisterPassengerCommand;
use OnlineBooking\src\Models\PassengerStatus;
use OnlineBooking\src\Repository\PassengerRepository;
use OnlineBooking\src\Services\UserService;

final readonly class PassengerService
{
    public function __construct(private PassengerRepository $passengerRepository, private UserService $userService){}

    public function registerPassenger(RegisterPassengerCommand $command) :  PassengerResponseDTO
    {
        $passenger = $this->userService->findUserById($command->userId);

        if ($this->passengerRepository->findPassengerByUserId($command->userId) !== null) {
           throw new PassengerAlreadyExistsException("Passenger already exists.", code: 409);
        }

        $passenger = new Passenger(
            passengerId: null,
            userId: $passenger->userId,
            fullName: $passenger->fullName,
            userName: $passenger->username,
            password: $passenger->getHashedPassword(),
            passengerStatus: $command->passengerStatus
        );

        $this->passengerRepository->save($passenger);
        return $this->mapToResponse($passenger);

    }

    public function getPassengerById(int $passengerId) : PassengerResponseDTO
    {
        return $this->mapToResponse($this->findPassengerById($passengerId));
    }

    public function getPassengerByUserId(int $userId) : PassengerResponseDTO
    {
        return $this->mapToResponse($this->findPassengerByUserId($userId));
    }

    public function updateStatus(int $passengerId, PassengerStatus $status) : void
    {
        $passenger = $this->findPassengerById($passengerId);
        $passenger->updateStatus($status);
        $this->passengerRepository->updatePassenger($passenger);
    }

    public function deletePassenger(int $passengerId) : void
    {
        $this->findPassengerById($passengerId);
        $this->passengerRepository->deletePassenger($passengerId);
    }

    private function findPassengerById(int $passengerId) : Passenger
    {
        return $this->passengerRepository->findPassengerById($passengerId)
        ?? throw new PassengerNotFoundException("Passenger not found with ID: $passengerId", code: 404);
    }

    private function findPassengerByUserId(int $userId) : Passenger
    {
        return $this->passengerRepository->findPassengerByUserId($userId)
        ?? throw new PassengerNotFoundException("Passenger not found with user ID: $userId", code: 404);
    }

    private function mapToResponse(Passenger $passenger) : PassengerResponseDTO
    {
        return new PassengerResponseDTO(
            passengerId: $passenger->passengerId,
            userId: $passenger->userId,
            fullName: $passenger->fullName,
            username: $passenger->username,
            passengerStatus: $passenger->passengerStatus->value
        );
    }
}