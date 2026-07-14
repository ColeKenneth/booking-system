<?php

namespace OnlineBooking\src\Services;

use OnlineBooking\src\Exceptions\AuthenticationException;
use OnlineBooking\src\Exceptions\InvalidDataException;
use OnlineBooking\src\Exceptions\UserAlreadyExistsException;
use OnlineBooking\src\Exceptions\UserNotFoundException;
use OnlineBooking\src\Models\User;
use OnlineBooking\src\Models\UserRole;
use OnlineBooking\src\Repository\UserRepository;
use RuntimeException;

final readonly class UserService
{
    public function __construct(private UserRepository $userRepository){}

    public function registerUser(string $fullName, string $username, string $password, UserRole $role = UserRole::PASSENGER) : User
    {
        $existing = $this->userRepository->findByUsername($username);
        if ($existing !== null) {
            error_log("Username [$username] already taken.");
            throw new UserAlreadyExistsException("Username $username is already taken.", code: 409);
        }

        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

        $user = new User(
            userId: null,
            fullName: $fullName,
            username: $username,
            password: $hashedPassword,
            userRole: $role
        );
        $this->userRepository->save($user);

        $savedUser = $this->userRepository->findByUsername($username);
        if ($savedUser === null) {
            throw new RuntimeException("Failed to retrieve saved user", code: 500);
        }
        return $savedUser;
    }

    public function authenticateUser(string $userName, string $plainTextPassword) : User
    {
        $user = $this->userRepository->findByUsername($userName);
        if ($user === null) {
            throw new AuthenticationException("Invalid username or password.", code: 404);
        }

        if (!$user->verifyPassword($plainTextPassword)) {
            throw new AuthenticationException("Invalid username or password.", code: 404);
        }

        return $user;
    }

    public function getUserById(int $userId) : User
    {
        $user = $this->userRepository->findByUserId($userId);

        if ($user === null) {
            throw new UserNotFoundException("User not found with ID: $userId", code: 404);
        }

        return $user;
    }

    public function getUserByUsername(string $username) : User
    {
        $user = $this->userRepository->findByUsername($username);

        if ($user === null) {
            throw new UserNotFoundException("User not found with username: $username", code: 404);
        }

        return $user;
    }

    public function updateUserProfile(int $userId, string $fullName, string $username) : User
    {
        $user = $this->getUserById($userId);
        $existing = $this->userRepository->findByUsername($username);

        if ($existing !== null && $existing->userId !== $userId) {
            throw new UserAlreadyExistsException("Username $username is already taken.", code: 409);
        }

        $user->fullName = $fullName;
        $user->username= $username;

        $this->userRepository->update($user);

        return $user;
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword) : void
    {
        $user = $this->getUserById($userId);

        if (!$user->verifyPassword($currentPassword)) {
            throw new InvalidDataException("Current password is incorrect.");
        }

        $user->validateNewPassword($newPassword);

        $hashedPassword = password_hash($newPassword, PASSWORD_ARGON2ID);
        $this->userRepository->updatePassword($userId, $hashedPassword);
    }

    public function deleteUser(int $userId) : void
    {
        $this->getUserById($userId);
        $this->userRepository->delete($userId);
    }

    public function usernameExists(string $username) : bool
    {
        return $this->userRepository->findByUsername($username) !== null;
    }

    public function validateCredentials(string $username, string $password) : bool
    {
        try {
            $this->authenticateUser($username, $password);
            return true;
        } catch (AuthenticationException) {
            return false;
        }

    }
}