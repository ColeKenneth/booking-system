<?php
declare(strict_types = 1);

namespace App\Enum;

enum PassengerStatus : string
{
    case ACTIVE = "Active";
    case IDLE = "Idle";
    case OFFLINE = "Offline";
}
