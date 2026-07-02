<?php
declare(strict_types=1);
namespace OnlineBooking\src\Models;

enum FareTier
{
    case ECO_CLASS;
    case STANDARD;

    case PREMIUM;

    public function getFareAmount() : float
    {
        return match ($this)
        {
            self::ECO_CLASS => 50.00,
            self::STANDARD => 75.00,
            self::PREMIUM => 100.00
        };
    }
}
