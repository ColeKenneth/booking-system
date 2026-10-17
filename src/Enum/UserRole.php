<?php
declare(strict_types = 1);

namespace App\Enum;

enum UserRole : string 
{
    case PASSENGER = "Passenger";
    case DRIVER = "Driver";
    case ADMIN = "Admin";
}