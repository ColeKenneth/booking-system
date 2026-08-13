<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

use OnlineBooking\src\Models\UserRole;

final readonly class UserResponseDTO
{
    public function __construct(
        public ?int $userId,
        public string $fullName,
        public string $username,
        public string $password,
        public string $userRole,
        public ?string $createdAt = null) 
    {}
}