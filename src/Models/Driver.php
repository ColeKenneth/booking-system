<?php
declare(strict_types=1);
namespace OnlineBooking\src\Models;

use OnlineBooking\src\Constants\BookingConstants;
use OnlineBooking\src\Contracts\Validate;
use OnlineBooking\src\Exceptions\InvalidDataException;
use Override;

class Driver extends User implements Validate
{
    public function __construct(
        public private(set) readonly ?int $driverId,
        ?int $userId,
        string $fullName,
        string $username,
        string $password,

        public string $carBrand {
            get => $this->carBrand;
            set {
                if (strlen(trim($value)) === 0) {
                    throw new InvalidDataException("Car brand is required.");
                }
                $this->carBrand = $value;
            }
        },

        public string $plateNumber {
            get => $this->plateNumber;
            set {
                $cleanedValue = strtoupper(trim($value));

                if (strlen($cleanedValue) === 0) {
                    throw new InvalidDataException("Plate number is required.");
                }

                if (!preg_match(BookingConstants::NUMBER_PLATE_FORMAT, $cleanedValue)) {
                    throw new InvalidDataException(BookingConstants::NUMBER_PLATE_HINT);
                }

                $this->plateNumber = $cleanedValue;
            }
        },

        public private(set) readonly DriverStatus $driverStatus
    ) {
        parent::__construct($userId, $fullName, $username, $password, UserRole::DRIVER);
    }

    #[Override]
    public function validate(): void
    {
        parent::validate();
        $this->carBrand = trim($this->carBrand);
        $this->plateNumber = trim($this->plateNumber);
    }

    #[Override]
    public static function fromArray(array $data) : static
    {
        return new static(
            (int)$data['driver_id'],
            (int)$data['user_id'],
            $data['full_name'],
            $data['username'],
            $data['password'],
            $data['car_brand'],
            $data['plate_number'],
            DriverStatus::from($data['driver_status'])
        );
    }
}