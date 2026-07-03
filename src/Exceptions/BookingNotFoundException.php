<?php

namespace OnlineBooking\src\Exceptions;

use Throwable;

final class BookingNotFoundException extends BookingException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}