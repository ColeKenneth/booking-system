<?php
declare(strict_types = 1);

namespace App\Enum;

enum BookingStatus : string
{
    case REQUESTED = "Requested";
    case ACCEPTED = "Accepted";
    case IN_PROGRESS = "In progress";
    case COMPLETED = "Completed";
    case CANCELLED = "Cancelled";
}