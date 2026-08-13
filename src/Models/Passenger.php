<?php
declare(strict_types=1);
namespace OnlineBooking\src\Models;

use DateTimeImmutable;
use OnlineBooking\src\Contracts\Validate;
use Override;

final class Passenger extends User implements Validate
{
    public function __construct(
        private(set) readonly ?int $passengerId,
        ?int $userId,
        string $fullName,
        string $userName,
        string $password,
        private(set) readonly PassengerStatus $passengerStatus,
        private ?DateTimeImmutable $createdAt = null
    )
    {
        parent::__construct($userId, $fullName, $userName, $password, UserRole::PASSENGER);
    }

    public string $getFormattedAt {
        get => $this->createdAt?->format('Y-m-d H:i:s');
    }

    #[Override]
    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['passenger_id'],
            (int)$data['user_id'],
            $data['full_name'],
            $data['username'],
            $data['password'],
            PassengerStatus::from($data['passenger_status']),
            isset($data['created_at']) ? new DateTimeImmutable($data['created_at']) : null
        );
    }

}
