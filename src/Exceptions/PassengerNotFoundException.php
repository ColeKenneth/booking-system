<?php

namespace OnlineBooking\src\Exceptions;

use OnlineBooking\src\Exceptions\BookingException;
use Throwable;

final class PassengerNotFoundException extends BookingException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}