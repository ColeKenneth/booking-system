<?php
declare(strict_types=1);
namespace OnlineBooking\src\Models;

enum BookingStatus : string
{
    case REQUESTED = "Requested";
    case ACCEPTED = "Accepted";
    case IN_PROGRESS = "In Progress";
    case COMPLETED = "Completed";
    case CANCELLED = "Cancelled";
}
