<?php
declare(strict_types=1);

namespace OnlineBooking\src\Repository;

use OnlineBooking\src\Exceptions\DriverNotFoundException;
use OnlineBooking\src\Models\Driver;
use PDO;
use PDOException;
use RuntimeException;

readonly class DriverRepository implements IDriverRepository
{
    public function __construct(private UserRepository $userRepository, private PDO $pdo){}

    public function saveDriver(Driver $driver): bool
    {
        try {
            $this->pdo->beginTransaction();
            $this->userRepository->save($driver);
            $userId = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare("INSERT INTO (user_id, car_brand, plate_number, driver_status) VALUES (:user_id, :car_brand, :plate_number, :driver_status)");
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':car_brand' => $driver->carBrand,
                ':plate_number' => $driver->plateNumber,
                ':driver_status' => $driver->driverStatus->value
            ]);

            $this->pdo->commit();
            return $result;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log("Error saving driver: [$driver->username] " . $e->getMessage());
            throw new RuntimeException("An error occurred while saving driver.", code: 500, previous: $e);
        }
    }

    public function findDriverById(int $driverId): ?Driver
    {
        try {
            $stmt = $this->pdo->prepare("SELECT u.user_id, u.full_name, u.username, u.password, u.user_role,
            d.driver_id, d.car_brand, d.plate_number, d.driver_status FROM drivers d JOIN users u ON d.user_id = u.user_id
            WHERE d.driver_id = :driver_id LIMIT 1");
            $stmt->execute([':driver_id' => $driverId]);
            $data = $stmt->fetch();

            if (!$data) return null;

            return Driver::fromArray($data);
        } catch (PDOException $e) {
            error_log("Error finding driver: [$driverId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while fetching driver.",
            code: 500, previous: $e);
        }
    }

    public function findAllDrivers(): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT u.user_id, u.full_name, u.username, u.password, u.user_role,
            d.driver_id, d.car_brand, d.plate_number, d.driver_status FROM drivers d JOIN users u ON d.user_id = u.user_id
            ORDER BY d.driver_id ASC");
            $stmt->execute();
            $results = $stmt->fetchAll();

            return $results ? array_map(fn($row) => Driver::fromArray($row), $results) : [];
        } catch (PDOException $e) {
            error_log("Error fetching all drivers: " . $e->getMessage());
            throw new RuntimeException("An error occurred while fetching all drivers.",
            code: 500, previous: $e);
        }
    }

    public function updateDriver(Driver $driver): bool
    {
        try {
            $this->pdo->beginTransaction();
            $this->userRepository->update($driver);

            $stmt = $this->pdo->prepare("UPDATE drivers SET car_brand = :car_brand, plate_number = :plate_number
            WHERE driver_id = :driver_id");
            $result = $stmt->execute([
                ':car_brand' => $driver->carBrand,
                ':plate_number' => $driver->plateNumber,
                ':driver_id' => $driver->driverId
            ]);

            $this->pdo->commit();
            return $result;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log("Error updating driver: [$driver->username] " . $e->getMessage());
            throw new RuntimeException("An error occurred while updating driver.", code: 500, previous: $e);
        }
    }

    public function updateStatus(int $driverId, string $status): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE drivers SET driver_status = :driver_status WHERE driver_id = :driver_id");

            return $stmt->execute([
                ':driver_status' => $status,
                ':driver_id' => $driverId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating driver status: [$driverId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while updating the driver's status.",
            code: 500, previous: $e);
        }
    }

    public function deleteDriver(int $driverId): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM drivers WHERE driver_id = :driver_id");
            $result = $stmt->execute([':driver_id' => $driverId]);
            if ($stmt->rowCount() === 0) throw new DriverNotFoundException("Driver not found.", code: 404);
            return $result;
        } catch (PDOException $e) {
            error_log("Error deleting driver: [$driverId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while deleting driver.", code: 500, previous: $e);
        }
    }
}