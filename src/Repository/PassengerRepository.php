<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Repository;

use OnlineBooking\src\Exceptions\PassengerNotFoundException;
use OnlineBooking\src\Models\Passenger;
use PDO;
use PDOException;
use RuntimeException;

readonly class PassengerRepository implements IPassengerRepository
{
    public function __construct(private UserRepository $userRepository, private PDO $pdo){}

    public function save(Passenger $passenger): bool
    {
        try {
            $this->pdo->beginTransaction();
            $this->userRepository->save($passenger);
            $userId = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare("INSERT INTO passengers (user_id, passenger_status) VALUES (:user_id, :passenger_status)");
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':passenger_status' => $passenger->passengerStatus->value
            ]);
            $this->pdo->commit();
            return $result;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log("Error saving passenger: [$passenger->username] " . $e->getMessage());
            throw new RuntimeException("An error occurred while saving passenger.",
            code: 500, previous: $e);
        }
    }

    public function findPassengerById(int $passengerId): ?Passenger
    {
        try {
            $stmt = $this->pdo->prepare("SELECT u.user_id, u.full_name, u.username, u.password,
            u.user_role, p.passenger_id, p.passenger_status FROM passengers p JOIN users u on p.user_id = u.user_id
            WHERE p.passenger_id = :passenger_id LIMIT 1");

            $stmt->execute([':passenger_id' => $passengerId]);
            $data = $stmt->fetch();

            if (!$data) return null;

            return Passenger::fromArray($data);
        } catch (PDOException $e) {
            error_log("Error fetching passenger: [$passengerId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while fetching passenger.",
            code: 500, previous: $e);
        }
    }

    public function findAllPassengers(): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT u.user_id, u.full_name, u.username, u.password, u.user_role,
            p.passenger_id, p.passenger_status FROM passengers p JOIN users u on p.user_id = u.user_id
            ORDER BY p.passenger_id");

            $stmt->execute();
            $results = $stmt->fetchAll();

            return $results ? array_map(fn($row) => Passenger::fromArray($row), $results) : [];
        } catch (PDOException $e) {
            error_log("Error fetching passengers: " . $e->getMessage());
            throw new RuntimeException("An error occurred while fetching all passengers.",
            code: 500, previous: $e);
        }
    }

    public function updatePassenger(Passenger $passenger): bool
    {
        try {
            $this->pdo->beginTransaction();
            $this->userRepository->update($passenger);
            $stmt = $this->pdo->prepare("UPDATE passengers SET passenger_status = :passenger_status WHERE passenger_id = :passenger_id");
            $result = $stmt->execute([':passenger_status' => $passenger->passengerStatus->value,
                ':passenger_id' => $passenger->passengerId]);

            $this->pdo->commit();
            return $result;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log("Error updating passenger: [$passenger->passengerId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while updating passenger.", code: 500, previous: $e);
        }
    }

    public function deletePassenger(int $passengerId): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM passengers WHERE passenger_id = :passenger_id");
            $result = $stmt->execute([':passenger_id' => $passengerId]);
            if ($stmt->rowCount() === 0) throw new PassengerNotFoundException("Passenger not found.", code: 404);
            return $result;
        } catch (PDOException $e) {
            error_log("Error deleting passenger: [$passengerId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while deleting passenger.",
            code: 500, previous: $e);
        }
    }
}