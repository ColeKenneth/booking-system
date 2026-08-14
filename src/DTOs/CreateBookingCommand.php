<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

final readonly class CreateBookingCommand
{
    public function __construct(
        public int $passengerId,
        public ?int $driverId,
        public string $pickupLocation,
        public string $dropOffLocation,
        public float $fare
    )
    {}
}