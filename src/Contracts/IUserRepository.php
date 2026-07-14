<?php
declare(strict_types=1);
namespace OnlineBooking\src\Repository;

use OnlineBooking\src\Models\User;

interface IUserRepository
{
    public function save(User $user) : bool;
    public function findByUsername(string $username) : ?User;
    public function findByUserId(int $userId) : ?User;
    public function update(User $user) : bool;
    public function updatePassword(int $userId, string $newHashedPassword) : bool;

    public function delete(int $userId) : bool;
}