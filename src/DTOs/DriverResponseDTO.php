<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

final readonly class DriverResponseDTO
{
    public function __construct(
        public int $driverId,
        public int $userId,
        public string $fullName,
        public string $username,
        public string $carBrand,
        public string $plateNumber,
        public string $driverStatus,
        public ?string $createdAt = null
    ) {}
}