<?php
declare(strict_types=1);

namespace OnlineBooking\src\Repository;

use OnlineBooking\src\Exceptions\UserAlreadyExistsException;
use OnlineBooking\src\Exceptions\UserNotFoundException;
use OnlineBooking\src\Models\User;
use PDO;
use PDOException;
use RuntimeException;

readonly class UserRepository implements IUserRepository
{
    public function __construct(private PDO $pdo){}

    public function save(User $user): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (full_name, username, password, user_role) VALUES (:full_name, :username, :password, :user_role)");

            return $stmt->execute([
                ':full_name' => $user->fullName,
                ':username' => $user->username,
                ':password' => $user->getHashedPassword(),
                ':user_role' => $user->userRole->value
            ]);
        } catch (PDOException $e) {
            if ((int)$e->getCode() === 23000) throw new UserAlreadyExistsException("User already exists.", code: 409);
            error_log("Error saving user: [$user->username]" . $e->getMessage());
            throw new RuntimeException("An error occurred while saving the user.", code: 500, previous: $e);
        }
    }

    public function findByUsername(string $username): ?User
    {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id, full_name, username, password, user_role FROM users WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $data = $stmt->fetch();

            if (!$data) return null;

            return User::fromArray($data);
        } catch (PDOException $e) {
            error_log("Error finding username: [$username] " . $e->getMessage());
            throw new RuntimeException("An error occurred while fetching username.",
            code: 500, previous: $e);
        }
    }

    public function findByUserId(int $userId): ?User
    {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id, full_name, username, password, user_role FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            $data = $stmt->fetch();

            if (!$data) return null;

            return User::fromArray($data);
        } catch (PDOException $e) {
            error_log("Error finding user ID: [[$userId]] " . $e->getMessage());
            throw new RuntimeException("An error occurred while fetching user ID.",
            code: 500, previous: $e);
        }
    }

    public function update(User $user): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET full_name = :full_name, username = :username,
                 user_role = :user_role WHERE user_id = :user_id");

            return $stmt->execute([
                ':full_name' => $user->fullName,
                ':username' => $user->username,
                ':user_role' => $user->userRole->value,
                ':user_id' => $user->userId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating user: [$user->userId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while updating user.",
            code: 500, previous: $e);
        }
    }

    public function updatePassword(int $userId, string $newHashedPassword): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");

            return $stmt->execute([
                ':password' => $newHashedPassword,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating password: [$userId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while updating password.",
            code: 500, previous: $e);
        }
    }

    public function delete(int $userId): bool
    {
        try {
            $user = $this->findByUserId($userId);
            if (!$user) throw new UserNotFoundException("User not found.", code: 404);

            $stmt = $this->pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Error deleting user: [$userId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while deleting user.",
            code: 500, previous: $e);
        }
    }
}