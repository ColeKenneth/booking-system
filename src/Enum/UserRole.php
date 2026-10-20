<?php
declare(strict_types = 1);

namespace App\Enum;

enum UserRole : string
{
    case ROLE_PASSENGER = "Passenger";
    case ROLE_DRIVER = "Driver";
    case ROLE_ADMIN = "Admin";
}
