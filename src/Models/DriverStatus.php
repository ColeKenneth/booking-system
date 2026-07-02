<?php
declare(strict_types=1);
namespace OnlineBooking\src\Models;

enum DriverStatus : string
{
    case ONLINE = "Driver is online.";
    case OFFLINE = "Driver is offline.";

    public function getStatus() : string
    {
        return $this->value;
    }
}
