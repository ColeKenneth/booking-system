<?php
declare(strict_types=1);

namespace OnlineBooking\src\Repository;

use OnlineBooking\src\Models\Driver;
use OnlineBooking\src\Models\DriverStatus;

interface IDriverRepository
{
    public function saveDriver(Driver $driver) : bool;
    public function findDriverById(int $driverId) : ?Driver;
    public function findDriverByUserId(int $userId) : ?Driver;
    public function findAllDrivers() : array;
    public function findDriverByPlateNumber(string $plateNumber) : ?Driver;
    public function updateDriver(Driver $driver) : bool;
    public function updateStatus(int $driverId, DriverStatus $status) : bool;
    public function deleteDriver(int $driverId) : bool;
}