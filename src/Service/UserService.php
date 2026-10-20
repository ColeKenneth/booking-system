<?php
declare(strict_types = 1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {}

    public function register(User $user, string $plainPassword): User
    {
        if (strlen(trim($plainPassword)) === 0) {
            throw new InvalidArgumentException("Password cannot be empty.");
        }

        return $this->entityManager->wrapInTransaction(function () use ($user, $plainPassword): User {
            if ($this->userRepository->findOneBy(['username' => $user->getUsername()]) !== null) {
                throw new DomainException("Username is already taken.");
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);

            return $user;
        });
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function findByUsername(string $username): ?User
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }

    public function updateProfile(User $user, string $firstName, ?string $middleName, string $lastName): User
    {
        $user
        ->setFirstName($firstName)
        ->setMiddleName($middleName)
        ->setLastName($lastName);

        $this->entityManager->flush();

        return $user;
    }

    public function delete(User $user) : void
    {
        $this->entityManager->wrapInTransaction(function () use ($user): void {
            $managedUser = $this->entityManager->contains($user)
            ? $user : $this->userRepository->find($user->getId());

            if ($managedUser === null) {
                throw new DomainException("User not found.");
            }

            $this->entityManager->remove($managedUser);
            $this->entityManager->flush();
        });

    }
}
