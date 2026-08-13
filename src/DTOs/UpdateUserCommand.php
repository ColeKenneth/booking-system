<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

final readonly class UpdateUserCommand
{
    public function __construct(
        public int $userId,
        public string $fullName,
        public string $username,
    )
    {}
}