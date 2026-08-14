<?php
declare(strict_types=1);

namespace OnlineBooking\src\DTOs;

use OnlineBooking\src\Models\BookingStatus;

final readonly class UpdateBookingCommand
{
    public function __construct(
        public int $bookingId,
        public ?int $driverId,
        public string $pickupLocation,
        public string $dropOffLocation,
        public float $fare,
        public BookingStatus $bookingStatus
    ) {}
}