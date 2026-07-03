<?php
declare(strict_types=1);
namespace OnlineBooking\src\Models;

use OnlineBooking\src\Contracts\Validate;
use OnlineBooking\src\Exceptions\InvalidDataException;

class Booking implements Validate
{
    public function __construct(
        private(set) readonly ?int $bookingId,
        private readonly ?int $passengerId,
        private readonly ?int $driverId,
        public string $pickupLocation {
            get => $this->pickupLocation;
            set {
                if (strlen(trim($value)) === 0) {
                    throw new InvalidDataException("Pickup location is required.");
                }
                $this->pickupLocation = $value;
            }
        },

        public string $dropOffLocation {
            get => $this->dropOffLocation;
            set {
                if (strlen(trim($value)) === 0) {
                    throw new InvalidDataException("Drop off location is required.");
                }

                $this->dropOffLocation = $value;
            }
        },

        public float $fare {
            get => $this->fare;
            set {
                if ($value <= 0.0) {
                    throw new InvalidDataException("Fare must be positive.");
                }

                $this->fare = $value;
            }
        },

        private(set) readonly BookingStatus $bookingStatus
    ){}

    public function getPassengerId() : ?int {
        return $this->passengerId;
    }

    public function getDriverId() : ?int {
        return $this->driverId;
    }

    public function validate(): void
    {
       $this->pickupLocation = trim($this->pickupLocation);
       $this->dropOffLocation = trim($this->dropOffLocation);
    }

    public static function fromArray(array $data) : static
    {
        return new static(
            (int)$data['booking_id'],
            (int)$data['passenger_id'],
            (int)$data['driver_id'],
            $data['pickup_location'],
            $data['drop_off_location'],
            (float)$data['fare'],
            BookingStatus::from($data['booking_status'])
        );
    }
}