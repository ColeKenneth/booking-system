<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

use OnlineBooking\src\Models\BookingStatus;

final readonly class RegisterBookingCommand
{
    public function __construct(
        public int $bookingId,
        public BookingStatus $status
    )
    {}
}