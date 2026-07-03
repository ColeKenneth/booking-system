<?php
declare(strict_types=1);

namespace OnlineBooking\src\Repository;

use OnlineBooking\src\Exceptions\BookingNotFoundException;
use RuntimeException;
use OnlineBooking\src\Models\Booking;
use PDO;
use PDOException;

readonly class BookingRepository implements IBookingRepository
{
    public function __construct(private PDO $pdo){}

    public function save(Booking $booking): bool
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO booking (passenger_id, driver_id, pickup_location, drop_off_location, fare, booking_status) 
                VALUES (:passenger_id, :driver_id, :pickup_location, :drop_off_location, :fare, :booking_status)");

            return $stmt->execute([
                ':passenger_id' => $booking->getPassengerId(),
                ':driver_id' => $booking->getDriverId(),
                ':pickup_location' => $booking->pickupLocation,
                ':drop_off_location' => $booking->dropOffLocation,
                ':fare' => $booking->fare,
                ':booking_status' => $booking->bookingStatus->value
            ]);
        } catch (PDOException $e) {
            error_log("Error saving booking: [$booking->bookingId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while saving booking.", code: 500, previous: $e);
        }
    }

    public function findBookingById(int $bookingId): ?Booking
    {
        try {
            $stmt = $this->pdo->prepare("SELECT b.*, 
                       p.passenger_id, u1.full_name as passenger_name,
                       d.driver_id, u2.full_name as driver_name
                FROM booking b
                JOIN passengers p ON b.passenger_id = p.passenger_id
                JOIN users u1 ON p.user_id = u1.user_id
                JOIN drivers d ON b.driver_id = d.driver_id
                JOIN users u2 ON d.user_id = u2.user_id
                WHERE b.booking_id = :booking_id
                LIMIT 1
            ");
            $stmt->execute([':booking_id' => $bookingId]);
            $data = $stmt->fetch();
            if (!$data) return null;

            return Booking::fromArray($data);
        } catch (PDOException $e) {
            error_log("Error fetching booking: [$bookingId] " . $e->getMessage());
            throw new RuntimeException("Error finding booking.", code: 500, previous: $e);
        }
    }

    public function findAllBookings(): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT b.*, p.passenger_id, u1.full_name as passenger_name,
            d.driver_id, u2.full_name as driver_name
            FROM booking b JOIN passengers p ON b.passenger_id = p.passenger_id 
            JOIN users u1 ON p.user_id = u1.user_id
            JOIN drivers d ON b.driver_id = d.driver_id
                JOIN users u2 ON d.user_id = u2.user_id
                ORDER BY b.booking_id DESC
            ");
            $stmt->execute();
            $results = $stmt->fetchAll();

            return array_map(fn($row) => Booking::fromArray($row), $results);
        } catch (PDOException $e) {
            error_log("Error fetching all bookings: " . $e->getMessage());
            throw new RuntimeException("Error fetching bookings.", code: 500, previous: $e);
        }
    }

    public function findBookingsByPassengerId(int $passengerId): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT b.*, d.driver_id, u2.full_name AS driver_name
            FROM booking b JOIN drivers d ON b.driver_id = d.driver_id
            JOIN users u2 ON d.user_id = u2.user_id WHERE b.passenger_id = :passenger_id
            ORDER BY b.booking_id ");

            $stmt->execute([':passenger_id' => $passengerId]);
            $results = $stmt->fetchAll();

            return array_map(fn($row) => Booking::fromArray($row), $results);
        } catch (PDOException $e) {
            error_log("Error fetching booking for passenger: [$passengerId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while fetching passenger bookings.",
            code: 500, previous: $e);
        }
    }

    public function findBookingsByDriverId(int $driverId): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT b.*, p.passenger_id, u1.full_name as passenger_name FROM booking b
                JOIN passengers p ON b.passenger_id = p.passenger_id
                JOIN users u1 ON p.user_id = u1.user_id
                WHERE b.driver_id = :driver_id
                ORDER BY b.booking_id");

            $stmt->execute([':driver_id' => $driverId]);
            $results = $stmt->fetchAll();

            return array_map(fn($row) => Booking::fromArray($row), $results);
        } catch (PDOException $e) {
            error_log("Error fetching bookings for driver: [$driverId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while fetching driver bookings.",
            code: 500, previous: $e);
        }
    }

    public function findBookingsByStatus(string $status): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT b.*, 
                       p.passenger_id, u1.full_name as passenger_name,
                       d.driver_id, u2.full_name as driver_name
                FROM booking b
                JOIN passengers p ON b.passenger_id = p.passenger_id
                JOIN users u1 ON p.user_id = u1.user_id
                JOIN drivers d ON b.driver_id = d.driver_id
                JOIN users u2 ON d.user_id = u2.user_id
                WHERE b.booking_status = :booking_status
                ORDER BY b.booking_id DESC
            ");

            $stmt->execute([':booking_status' => $status]);
            $results = $stmt->fetchAll();

            return array_map(fn($row) => Booking::fromArray($row), $results);
        } catch (PDOException $e) {
            error_log("Error finding bookings with status: [$status] " . $e->getMessage());
            throw new RuntimeException("An error occurred while fetching bookings by status.",
            code: 500, previous: $e);
        }
    }

    public function updateBookingStatus(int $bookingId, string $status): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE booking SET booking_status = :booking_status WHERE booking_id = :booking_id");

            return $stmt->execute([
                ':booking_status' => $status,
                ':booking_id' => $bookingId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating booking status: [$bookingId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while updating booking status.",
            code: 500, previous: $e);
        }
    }

    public function updateBooking(Booking $booking): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE booking SET passenger_id = :passenger_id,
                   driver_id = :driver_id, pickup_location = :pickup_location, drop_off_location = :drop_off_location,
                   fare = :fare, booking_status = :booking_status WHERE booking_id = :booking_id");

            return $stmt->execute([
                ':passenger_id' => $booking->getPassengerId(),
                ':driver_id' => $booking->getDriverId(),
                ':pickup_location' => $booking->pickupLocation,
                ':drop_off_location' => $booking->dropOffLocation,
                ':fare' => $booking->fare,
                ':booking_status' => $booking->bookingStatus->value,
                ':booking_id' => $booking->bookingId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating booking: [$booking->bookingId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while updating booking.",
            code: 500, previous: $e);
        }
    }

    public function deleteBooking(int $bookingId): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM booking WHERE booking_id = :booking_id");
            $result = $stmt->execute([':booking_id' => $bookingId]);

            if ($stmt->rowCount() === 0) throw new BookingNotFoundException("Booking not found.", code: 404);

            return $result;
        } catch (PDOException $e) {
            error_log("Error deleting booking: [$bookingId] " . $e->getMessage());
            throw new RuntimeException("An error occurred while deleting booking.",
            code: 500, previous: $e);
        }
    }
}