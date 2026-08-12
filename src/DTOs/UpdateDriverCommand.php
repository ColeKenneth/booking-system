<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

final readonly class UpdateDriverCommand
{
    public function __construct(
        public int $driverId,
        public string $carBrand,
        public string $plateNumber
    )
    {}
}