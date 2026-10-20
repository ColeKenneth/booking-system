<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\Passenger;
use App\Entity\User;
use App\Enum\PassengerStatus;
use App\Repository\PassengerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;

final readonly class PassengerService
{
    public function __construct(
        private readonly PassengerRepository $passengerRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {}

    public function create(User $user): Passenger
    {
        return $this->entityManager->wrapInTransaction(function () use ($user): Passenger {
            $existingPassenger = $this->passengerRepository->findOneBy(['user' => $user]);

            if ($existingPassenger !== null) {
                throw new DomainException("User is already registered as a passenger.");
            }

            $passenger = new Passenger($user);

            $this->entityManager->persist($passenger);
            $this->entityManager->flush();

            return $passenger;
        });
    }

    public function findById(int $id): ?Passenger
    {
        return $this->passengerRepository->find($id);
    }

    public function findByUser(User $user): ?Passenger
    {
        return $this->passengerRepository->findOneBy(['user' => $user]);
    }

    public function updateStatus(Passenger $passenger, PassengerStatus $status): Passenger
    {
        $passenger->setPassengerStatus($status);

        $this->entityManager->flush();

        return $passenger;
    }

    public function delete(Passenger $passenger): void
    {
        $this->entityManager->wrapInTransaction(function() use ($passenger): void {
            $managedPassenger = $this->entityManager->contains($passenger)
            ? $passenger : $this->passengerRepository->find($passenger->getId());

            if ($managedPassenger === null) {
                throw new DomainException("Passenger not found.");
            }

            $this->entityManager->remove($managedPassenger);
            $this->entityManager->flush();
        });
    }
}
