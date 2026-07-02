<?php
declare(strict_types=1);
namespace OnlineBooking\src\Models;

use OnlineBooking\src\Contracts\Validate;
use Override;

class Passenger extends User implements Validate
{
    public function __construct(
        public private(set) readonly ?int $passengerId,
        ?int $userId,
        string $fullName,
        string $userName,
        string $password,
        public private(set) readonly PassengerStatus $passengerStatus
    )
    {
        parent::__construct($userId, $fullName, $userName, $password, UserRole::PASSENGER);
    }

    #[Override]
    public static function fromArray(array $data): static
    {
        return new static(
            (int)$data['passenger_id'],
            (int)$data['user_id'],
            $data['full_name'],
            $data['username'],
            $data['password'],
            PassengerStatus::from($data['passenger_status'])
        );
    }

}
