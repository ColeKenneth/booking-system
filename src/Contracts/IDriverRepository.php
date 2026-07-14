<?php
declare(strict_types=1);

namespace OnlineBooking\src\Repository;

use OnlineBooking\src\Models\Driver;

interface IDriverRepository
{
    public function saveDriver(Driver $driver) : bool;
    public function findDriverById(int $driverId) : ?Driver;
    public function findAllDrivers() : array;
    public function updateDriver(Driver $driver) : bool;
    public function updateStatus(int $driverId, string $status) : bool;
    public function deleteDriver(int $driverId) : bool;
}