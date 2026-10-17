<?php
declare(strict_types = 1);

namespace App\Enum;

enum DriverStatus : string
{
    case ONLINE = "Driver is online.";
    case OFFILNE = "Driver is offline.";
    case IN_TRANSIT = "Driver is in transit.";
    case IDLE = "Driver is idle.";
}
