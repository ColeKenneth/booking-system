<?php

use OnlineBooking\src\Controllers\BookingController;
use OnlineBooking\src\Controllers\DriverController;
use OnlineBooking\src\Controllers\PassengerController;
use OnlineBooking\src\Controllers\UserController;
use OnlineBooking\src\DTOs\ChangePasswordCommand;
use OnlineBooking\src\DTOs\RegisterUserCommand;
use OnlineBooking\src\DTOs\UpdateUserCommand;
use OnlineBooking\src\Models\UserRole;
use OnlineBooking\src\DTOs\CreateBookingCommand;
use OnlineBooking\src\DTOs\RegisterDriverCommand;
use OnlineBooking\src\DTOs\RegisterPassengerCommand;
use OnlineBooking\src\DTOs\UpdateBookingCommand;
use OnlineBooking\src\DTOs\UpdateDriverCommand;
use OnlineBooking\src\Models\BookingStatus;
use OnlineBooking\src\Models\DriverStatus;
use OnlineBooking\src\Models\PassengerStatus;

require_once __DIR__ . '/../src/bootstrap.php';
/**
 * This is for the controllers to route and process data coming from the frontend.
 */
$bookingController = new BookingController($bookingService);
$driverController = new DriverController($driverService);
$passengerController = new PassengerController($passengerService);
$userController = new UserController($userService);

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

if ($method === "GET" && preg_match('#^/drivers/user/(\d+)$#', $uri, $matches)) {
    $userId = (int)$matches[1];

    $result = $driverController->getByUserId($userId);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === "GET" && preg_match('#^/drivers/(\d+)$#', $uri, $matches)) {
    $driverId = (int)$matches[1];

    $result = $driverController->getById($driverId);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === "PUT" && preg_match('#^/drivers/(\d+)$#', $uri, $matches)) {
    $driverId = (int)$matches[1];
    $data = json_encode(
        file_get_contents('php://input'),
        true,
        JSON_THROW_ON_ERROR
    );

    $command = new UpdateDriverCommand(
        driverId: (int)$data['driver_id'],
        carBrand: $data['car_brand'],
        plateNumber: $data['plate_number']
    );

    $result = $driverController->update($command);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === "PATCH" && preg_match('#^/drivers/(\d+)/status$#', $uri, $matches)) {
    $driverId = (int)$matches[1];

    $data = json_encode(
        file_get_contents('php://input'),
        true,
        JSON_THROW_ON_ERROR
    );

    $driverStatus = DriverStatus::from($data['driver_status']);

    $driverController->updateByStatus($driverId, $driverStatus);
    http_response_code(204);
    exit;
}

if ($method === "DELETE" && preg_match('#^/drivers/(\d+)$#', $uri, $matches)) {
    $driverId = (int)$matches[1];

    $driverController->delete($driverId);

    http_response_code(204);
    exit;
}

// Passenger Repository part
if ($method === 'POST' && $uri === '/passengers') {
    $data = json_decode(
        file_get_contents('php://input'),
        true,
        flags: JSON_THROW_ON_ERROR
    );

    $command = new RegisterPassengerCommand(
        userId: (int) $data['user_id'],
        passengerStatus: PassengerStatus::from($data['passenger_status'])
    );

    $result = $passengerController->register($command);

    header('Content-Type: application/json');
    http_response_code(201);
    echo json_encode($result);
    exit;
}

if ($method === 'GET' && preg_match('#^/passengers/user/(\d+)$#', $uri, $matches)) {
    $userId = (int) $matches[1];

    $result = $passengerController->getByUserId($userId);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === 'GET' && preg_match('#^/passengers/(\d+)$#', $uri, $matches)) {
    $passengerId = (int) $matches[1];

    $result = $passengerController->getById($passengerId);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === 'PATCH' && preg_match('#^/passengers/(\d+)/status$#', $uri, $matches)) {
    $passengerId = (int) $matches[1];

    $data = json_decode(
        file_get_contents('php://input'),
        true,
        flags: JSON_THROW_ON_ERROR
    );

    $status = PassengerStatus::from($data['status']);

    $passengerController->updateStatus($passengerId, $status);

    http_response_code(204);
    exit;
}

if ($method === 'DELETE' && preg_match('#^/passengers/(\d+)$#', $uri, $matches)) {
    $passengerId = (int) $matches[1];

    $passengerController->delete($passengerId);

    http_response_code(204);
    exit;
}

// User Controller part

if ($method === 'POST' && $uri === '/users') {
    $data = json_decode(
        file_get_contents('php://input'),
        true,
        flags: JSON_THROW_ON_ERROR
    );

    $command = new RegisterUserCommand(
        fullName: $data['full_name'],
        username: $data['username'],
        password: $data['password'],
        userRole: UserRole::from($data['user_role'])
    );

    $result = $userController->register($command);

    header('Content-Type: application/json');
    http_response_code(201);
    echo json_encode($result);
    exit;
}

if ($method === 'POST' && $uri === '/users/authenticate') {
    $data = json_decode(
        file_get_contents('php://input'),
        true,
        flags: JSON_THROW_ON_ERROR
    );

    $result = $userController->authenticate(
        $data['username'],
        $data['password']
    );

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === 'GET' && preg_match('#^/users/username/(.+)$#', $uri, $matches)) {
    $username = urldecode($matches[1]);

    $result = $userController->getByUsername($username);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === 'GET' && preg_match('#^/users/(\d+)$#', $uri, $matches)) {
    $userId = (int) $matches[1];

    $result = $userController->getById($userId);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === 'PUT' && preg_match('#^/users/(\d+)$#', $uri, $matches)) {
    $userId = (int) $matches[1];

    $data = json_decode(
        file_get_contents('php://input'),
        true,
        flags: JSON_THROW_ON_ERROR
    );

    $command = new UpdateUserCommand(
        userId: $userId,
        fullName: $data['full_name'],
        username: $data['username']
    );

    $result = $userController->update($command);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

if ($method === 'PATCH' && preg_match('#^/users/(\d+)/password$#', $uri, $matches)) {
    $userId = (int) $matches[1];

    $data = json_decode(
        file_get_contents('php://input'),
        true,
        flags: JSON_THROW_ON_ERROR
    );

    $command = new ChangePasswordCommand(
        userId: $userId,
        currentPassword: $data['current_password'],
        newPassword: $data['new_password']
    );

    $userController->changePassword($command);

    http_response_code(204);
    exit;
}

if ($method === 'DELETE' && preg_match('#^/users/(\d+)$#', $uri, $matches)) {
    $userId = (int) $matches[1];

    $userController->delete($userId);

    http_response_code(204);
    exit;
}

http_response_code(404);

header('Content-Type: application/json');

echo json_encode([
    'message' => 'Route not found.'
]);
