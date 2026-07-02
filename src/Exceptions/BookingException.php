<?php

namespace OnlineBooking\src\Exceptions;
use RuntimeException;
use Throwable;

class BookingException extends RuntimeException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}