<?php
declare(strict_types=1);

namespace OnlineBooking\src\Controllers;

use OnlineBooking\src\Models\User;
use OnlineBooking\src\DTOs\UserResponseDTO;
use OnlineBooking\src\DTOs\RegisterUserCommand;
use OnlineBooking\src\DTOs\UpdateUserCommand;
use OnlineBooking\src\DTOs\ChangePasswordCommand;
use OnlineBooking\src\Services\UserService;

final readonly class UserController
{
    public function __construct(
        private UserService $userService
    ) {}

    public function register(RegisterUserCommand $command): UserResponseDTO
    {
        return $this->userService->registerUser($command);
    }

    public function authenticate(string $username, string $password): User
    {
        return $this->userService->authenticateUser($username, $password);
    }

    public function getById(int $userId): UserResponseDTO
    {
        return $this->userService->getUserById($userId);
    }

    public function getByUsername(string $username): UserResponseDTO
    {
        return $this->userService->getUserByUsername($username);
    }

    public function update(UpdateUserCommand $command): UserResponseDTO
    {
        return $this->userService->updateUserProfile($command);
    }

    public function changePassword(ChangePasswordCommand $command): void {
        $this->userService->changePassword(
            $command->userId,
            $command->currentPassword,
            $command->newPassword
        );
    }

    public function delete(int $userId): void
    {
        $this->userService->deleteUser($userId);
    }
}