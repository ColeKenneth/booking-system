<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

use OnlineBooking\src\Models\DriverStatus;

readonly class RegisterDriverCommand 
{
    public function __construct(
        public int $userId,
        public string $carBrand,
        public string $plateNumber,
        public DriverStatus $driverStatus = DriverStatus::OFFLINE
    )
    {}
}