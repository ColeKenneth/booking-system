<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Models;

enum UserRole : string
{
    case PASSENGER = "Passenger";
    case DRIVER = "Driver";
    case ADMIN = "Admin";
}
