<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Exceptions;

use OnlineBooking\src\Exceptions\BookingException;
use Throwable;
use Override;

final class PassengerAlreadyExistsException extends BookingException
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}