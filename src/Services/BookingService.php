<?php
declare(strict_types = 1);

namespace OnlineBooking\src\Services;

use OnlineBooking\src\DTOs\BookingResponseDTO;
use OnlineBooking\src\DTOs\CreateBookingCommand;
use OnlineBooking\src\DTOs\UpdateBookingCommand;
use OnlineBooking\src\Exceptions\BookingNotFoundException;
use OnlineBooking\src\Models\Booking;
use OnlineBooking\src\Models\BookingStatus;
use OnlineBooking\src\Repository\BookingRepository;

final readonly class BookingService
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private PassengerService $passengerService,
        private DriverService $driverService
    ){}

    public function registerBooking(CreateBookingCommand $command) : BookingResponseDTO
    {
        $this->passengerService->getPassengerById($command->passengerId);
        
        if ($command->driverId !== null) {
            $this->driverService->getDriverById($command->driverId);
        }

        $booking = new Booking(
            bookingId: null,
            passengerId: $command->passengerId,
            driverId: $command->driverId,
            pickupLocation: $command->pickupLocation,
            dropOffLocation: $command->dropOffLocation,
            fare: $command->fare,
            bookingStatus: BookingStatus::REQUESTED
        );

        $booking->validate();

        $this->bookingRepository->save($booking);

        return $this->mapToResponse($booking);
    }

    public function getBookingById(int $bookingId) : BookingResponseDTO
    {
        return $this->mapToResponse($this->findBookingById($bookingId));
    }

    public function getBookingsByPassenger(int $passengerId) : array
    {
        return array_map(fn(Booking $booking) => $this->mapToResponse($booking),
        $this->bookingRepository->findBookingsByPassengerId($passengerId));
    }

    public function getBookingsByStatus(BookingStatus $status) : array
    {
        return array_map(fn(Booking $booking) => $this->mapToResponse($booking),
        $this->bookingRepository->findBookingsByStatus($status->value));
    }

    public function getAllBookings() : array
    {
        return array_map(fn(Booking $booking) => $this->mapToResponse($booking),
        $this->bookingRepository->findAllBookings());
    }

    public function updateBooking(UpdateBookingCommand $command) : BookingResponseDTO
    {
        $booking = $this->findBookingById($command->bookingId);

        $booking->pickupLocation = $command->pickupLocation;
        $booking->dropOffLocation = $command->dropOffLocation;
        $booking->fare = $command->fare;

        $booking->validate();

        $this->bookingRepository->updateBooking($booking);

        return $this->mapToResponse($booking);
    }

    public function updateStatus(int $bookingId, BookingStatus $status) : void
    {
        $booking = $this->findBookingById($bookingId);

        $booking->updateStatus($status);

        $this->bookingRepository->updateBookingStatus($bookingId, $status->value);
    }

    public function deleteBooking(int $bookingId) : void
    {
        $this->findBookingById($bookingId);
        $this->bookingRepository->deleteBooking($bookingId);
    }

    private function findBookingById(int $bookingId) : Booking
    {
        return $this->bookingRepository->findBookingById($bookingId)
        ?? throw new BookingNotFoundException("Booking not found with ID: $bookingId", code: 404);
    }

    private function mapToResponse(Booking $booking) : BookingResponseDTO
    {
        return new BookingResponseDTO(
            bookingId: $booking->bookingId,
            passengerId: $booking->getPassengerId(),
            driverId: $booking->getDriverId(),
            pickupLocation: $booking->pickupLocation,
            dropOffLocation: $booking->dropOffLocation,
            fare: $booking->fare,
            bookingStatus: $booking->bookingStatus->value,
            createdAt: $booking->formattedAt
        );
    }
}