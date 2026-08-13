<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

use OnlineBooking\src\Models\UserRole;

final readonly class RegisterUserCommand
{
    public function __construct(
        public string $fullName,
        public string $username,
        public string $password,
        public UserRole $userRole = UserRole::PASSENGER
    )
    {}
}