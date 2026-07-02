<?php
declare(strict_types=1);
namespace OnlineBooking\src\Models;

enum PassengerStatus : string
{
    case ACTIVE = "Active";
    case IDLE = "Idle";
}