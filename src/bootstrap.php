<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use OnlineBooking\src\Connection\DBConnection;
use OnlineBooking\src\Repository\BookingRepository;
use OnlineBooking\src\Repository\DriverRepository;
use OnlineBooking\src\Repository\PassengerRepository;
use OnlineBooking\src\Repository\UserRepository;
use OnlineBooking\src\Services\BookingService;
use OnlineBooking\src\Services\DriverService;
use OnlineBooking\src\Services\PassengerService;
use OnlineBooking\src\Services\UserService;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$pdo = DBConnection::getInstance();

$userRepository = new UserRepository($pdo);
$driverRepository = new DriverRepository($userRepository, $pdo);
$passengerRepository = new PassengerRepository($userRepository, $pdo);
$bookingRepository = new BookingRepository($pdo);

$userService = new UserService($userRepository);
$driverService = new DriverService($driverRepository, $userService);
$passengerService = new PassengerService($passengerRepository, $userService);
$bookingService = new BookingService($bookingRepository, $passengerService, $driverService);
