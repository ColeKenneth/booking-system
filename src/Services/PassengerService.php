<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

use OnlineBooking\src\Exceptions\PassengerAlreadyExistsException;
use OnlineBooking\src\Exceptions\PassengerNotFoundException;
use OnlineBooking\src\Models\Passenger;
use OnlineBooking\src\Repository\PassengerRepository;
use OnlineBooking\src\Services\UserService;

final readonly class PassengerService
{
    public function __construct(private PassengerRepository $passengerRepository, private UserService $userService){}

    public function registerPassenger(RegisterPassengerCommand $command) :  PassengerResponseDTO
    {
        $passenger = $this->userService->getUserById($command->userId);

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

    public function getPassengerById(int $passengerId) : Passenger
    {
        $passenger = $this->passengerRepository->findPassengerById($passengerId)
        ?? throw new PassengerNotFoundException("Passenger not found with ID: $passengerId", code: 404);
        return $passenger;
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