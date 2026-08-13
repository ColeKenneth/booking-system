<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

final readonly class PassengerResponseDTO 
{
    public function __construct(
        public int $passengerId,
        public int $userId,
        public string $fullName,
        public string $username,
        public string $passengerStatus
    )
    {}
}