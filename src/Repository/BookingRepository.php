<?php

namespace OnlineBooking\src\Repository;

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
            $stmt = $this->pdo->prepare("INSERT INTO bookings (passenger_id, driver_id, pickup_location, drop_off_location, fare, booking_status) 
                VALUES (:passenger_id, :driver_id, :pickup_location, :drop_off_location, :fare, :booking_status");

            return $stmt->execute([
                ':passenger_id' => $booking->getPassengerId(),
                ':driver_id' => $booking->getDriverId(),
                ':pickup_location' => $booking->pickupLocation,
                ':drop_off_location' => $booking->dropOffLocation,
                ':fare' => $booking->fare,
                ':booking_status' => $booking->bookingStatus->value
            ]);
        } catch (PDOException $e) {
            error_log("Error saving booking: [{$booking->bookingId}] " . $e->getMessage());
            throw new RuntimeException("An error occurred while saving booking.", code: 500, previous: $e);
        }
    }

    public function findBookingById(int $bookingId): ?Booking
    {
        try {
            $stmt = $this->pdo->prepare("SELECT b.*, 
                       p.passenger_id, u1.full_name as passenger_name,
                       d.driver_id, u2.full_name as driver_name
                FROM bookings b
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
            error_log("Error fetching booking: [{$bookingId}] " . $e->getMessage());
            throw new RuntimeException("Error finding booking.", code: 500, previous: $e);
        }
    }

    public function findAllBookings(): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT b.*, p.passenger_id, u1.full_name as passenger_name,
            d.driver_id, u2.full_name as driver_name
            FROM bookings b JOIN passengers p ON b.passenger_id = p.passenger_id 
            JOIN users u1 ON p.user_id = u1.user_id
            JOIN drivers d ON b.driver_id = d.driver_id
                JOIN users u2 ON d.user_id = u2.user_id
                ORDER BY b.booking_id DESC
            ");
            $stmt->execute();
            $results = $stmt->fetchAll();

            return $results ? array_map(fn($row) => Booking::fromArray($row), $results) : [];
        } catch (PDOException $e) {
            error_log("Error fetching all bookings: " . $e->getMessage());
            throw new RuntimeException("Error fetching bookings.", code: 500, previous: $e);
        }
    }

    public function findBookingsByPassengerId(int $passengerId): array
    {
        // TODO: Implement findBookingsByPassengerId() method.
    }

    public function findBookingsByDriverId(int $driverId): array
    {
        // TODO: Implement findBookingsByDriverId() method.
    }

    public function findBookingsByStatus(string $status): array
    {
        // TODO: Implement findBookingsByStatus() method.
    }

    public function updateBookingStatus(int $bookingId, string $status): bool
    {
        // TODO: Implement updateBookingStatus() method.
    }

    public function updateBooking(Booking $booking): bool
    {
        // TODO: Implement updateBooking() method.
    }

    public function deleteBooking(int $bookingId): bool
    {
        // TODO: Implement deleteBooking() method.
    }
}