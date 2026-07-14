<?php
declare(strict_types=1);
namespace OnlineBooking\src\Repository;

use OnlineBooking\src\Models\Passenger;

interface IPassengerRepository
{
    public function save(Passenger $passenger) : bool;
    public function findPassengerById(int $passengerId) : ?Passenger;
    public function findAllPassengers() : array;
    public function updatePassenger(Passenger $passenger) : bool;
    public function deletePassenger(int $passengerId) : bool;
}