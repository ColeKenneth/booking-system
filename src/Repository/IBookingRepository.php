<?php

namespace OnlineBooking\src\Repository;

use OnlineBooking\src\Models\Booking;

interface IBookingRepository
{
    public function save(Booking $booking) : bool;
    public function findBookingById(int $bookingId) : ?Booking;
    public function findAllBookings() : array;
    public function findBookingsByPassengerId(int $passengerId) : array;
    public function findBookingsByDriverId(int $driverId) : array;
    public function findBookingsByStatus(string $status) : array;
    public function updateBookingStatus(int $bookingId, string $status) : bool;
    public function updateBooking(Booking $booking) : bool;
    public function deleteBooking(int $bookingId) : bool;
}