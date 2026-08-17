<?php

use OnlineBooking\src\Controllers\BookingController;
use OnlineBooking\src\DTOs\CreateBookingCommand;
use OnlineBooking\src\DTOs\RegisterDriverCommand;
use OnlineBooking\src\DTOs\UpdateBookingCommand;
use OnlineBooking\src\Models\BookingStatus;
use OnlineBooking\src\Models\DriverStatus;

require_once __DIR__ . '/../src/bootstrap.php';
/**
 * This is for the Booking Controller to route and process data coming from the frontend.
 */
$bookingController = new BookingController($bookingService);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === "POST" && $uri === "/bookings") {
    try {
        $data = json_encode(
            file_get_contents('php://input'),
            true,
            JSON_THROW_ON_ERROR
        );

        $command = new CreateBookingCommand(
            passengerId: (int) $data['passenger_id'],
            driverId: isset($data['driver_id']) ? (int) $data['driver_id'] : null,
            pickupLocation: $data['pickup_location'],
            dropOffLocation: $data['drop_off_location'],
            fare: (float) $data['fare']
        );

        $result = $bookingController->create($command);
        http_response_code(201);
        header('Content-Type: application/json');

        echo json_encode($result);
        exit;
    } catch (JsonException) {
        http_response_code(400);
        header('Content-Type: application/json');

        echo json_encode([
            'message' => 'Invalid JSON.'
        ]);
        exit;
    }
}

if ($method === "GET" && $uri === "/bookings") {
    $result = $bookingController->getAll();

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === "GET" && preg_match('#^/bookings/(\d+)$#', $uri, $matches)) {
    $bookingId = (int)$matches[1];

    $result = $bookingController->getById($bookingId);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === "GET" && preg_match('#^/bookings/passenger/(\d+)$#', $uri, $matches)) {
    $passengerId = (int)$matches[1];

    $result = $bookingController->getByPassenger($passengerId);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === "GET" && preg_match('#^/bookings/status/([^/]+)$#', $uri, $matches)) {
    $status = $matches[1];

    $result = $bookingController->getByStatus($status);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === "PUT" && preg_match('#^/bookings/(\d+)$#', $uri, $matches)) {
    $bookingId = (int)$matches[1];
    $data = json_encode(
        file_get_contents('php://input'),
        true,
        JSON_THROW_ON_ERROR
    );

    $command = new UpdateBookingCommand(
        bookingId: $bookingId,
        driverId: isset($data['driver_id']) ? (int) $data['driver_id'] : null,
        pickupLocation: $data['pickup_location'],
        dropOffLocation: $data['drop_off_location'],
        fare: (float) $data['fare'],
        bookingStatus: BookingStatus::from($data['booking_status'])
    );

    $result = $bookingController->update($command);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === "PATCH" && preg_match('#^/bookings/(\d+)/status$#', $uri, $matches)) {
    $bookingId = (int)$matches[1];
    $data = json_encode(
        file_get_contents('php://input'),
        true,
        JSON_THROW_ON_ERROR
    );

    $status = BookingStatus::from($data['booking_status']);

    $bookingController->updateStatus($bookingId, $status);

    http_response_code(204);
    exit;
}

if ($method === "DELETE" && preg_match('#^/bookings/(\d+)$#', $uri, $matches)) {
    $bookingId = (int)$matches[1];

    $bookingController->delete($bookingId);

    http_response_code(204);
    exit;
}

// Driver Controller part
if ($method === "POST" && $uri === "/drivers") {
    $data = json_encode(
        file_get_contents('php://input'),
        true,
        JSON_THROW_ON_ERROR
    );

    $command = new RegisterDriverCommand(
        userId: (int)$data['user_id'],
        carBrand: $data['car_brand'],
        plateNumber: $data['plate_number'],
        driverStatus: DriverStatus::from($data['driver_status'])
    );

    $result = $driverController->register($command);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

http_response_code(404);

header('Content-Type: application/json');

echo json_encode([
    'message' => 'Route not found.'
]);
