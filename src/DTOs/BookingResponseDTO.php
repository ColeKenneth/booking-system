<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

final readonly class BookingResponseDTO
{
    public function __construct(
        public int $bookingId,
        public int $passengerId,
        public int $driverId,
        public string $pickupLocation,
        public string $dropOffLocation,
        public float $fare,
        public string $bookingStatus,
        public ?string $createdAt = null
    ){}
}