<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

use OnlineBooking\src\Models\PassengerStatus;

final readonly class RegisterPassengerCommand
{
    public function __construct(
        public int $userId,
        public PassengerStatus $passengerStatus = PassengerStatus::ACTIVE
    )
    {}
}