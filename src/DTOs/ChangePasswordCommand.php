<?php
declare(strict_types = 1);

namespace OnlineBooking\src\DTOs;

final readonly class ChangePasswordCommand
{
    public function __construct(
        public int $userId,
        public string $currentPassword,
        public string $newPassword
    ){}
}