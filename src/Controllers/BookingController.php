<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Controllers;

use OnlineBooking\src\DTOs\BookingResponseDTO;
use OnlineBooking\src\DTOs\CreateBookingCommand;
use OnlineBooking\src\DTOs\UpdateBookingCommand;
use OnlineBooking\src\Models\BookingStatus;
use OnlineBooking\src\Services\BookingService;

final readonly class BookingController
{
    public function __construct(private BookingService $bookingService){}
    
    public function create(CreateBookingCommand $command) : BookingResponseDTO
    {
        return $this->bookingService->registerBooking($command);
    }

    public function getById(int $bookingId) : BookingResponseDTO
    {
        return $this->bookingService->getBookingById($bookingId);
    }

    public function getAll() : array
    {
        return $this->bookingService->getAllBookings();
    }

    public function getByPassenger(int $passengerId) : array
    {
        return $this->bookingService->getBookingsByPassenger($passengerId);
    }

    public function getByStatus(string $status) : array
    {
        return $this->bookingService->getBookingsByStatus(BookingStatus::from($status));
    }

    public function update(UpdateBookingCommand $command) : BookingResponseDTO
    {
        return $this->bookingService->updateBooking($command);
    }

    public function updateStatus(int $bookingId, BookingStatus $bookingStatus) : void
    {
        $this->bookingService->updateStatus($bookingId, $bookingStatus);
    }

    public function delete(int $bookingId) : void
    {
        $this->bookingService->deleteBooking($bookingId);
    }
}