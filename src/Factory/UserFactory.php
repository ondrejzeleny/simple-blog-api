<?php

namespace App\Factory;

use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RoleConverter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Factory for creating and updating User entities.
 */
class UserFactory implements UserFactoryInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly RoleConverter $roleConverter,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * Creates a new User entity from a DTO.
     */
    public function createFromDto(UserCreateDto $dto): User
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $dto->email]);
        if ($existingUser) {
            throw new \InvalidArgumentException('User with this email already exists.');
        }

        $user = new User($dto->name, $dto->email);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
        $role = $this->roleConverter->toSystemRole($dto->role);

        $user->setPassword($hashedPassword);
        $user->setRole($role);

        return $user;
    }

    /**
     * Updates an existing User entity from a DTO.
     */
    public function updateFromDto(User $user, UserUpdateDto $dto): User
    {
        if (!is_null($dto->name)) {
            $user->setName($dto->name);
        }

        if (!is_null($dto->email)) {
            if ($dto->email !== $user->getEmail()) {
                $existingUser = $this->userRepository->findOneBy(['email' => $dto->email]);
                if ($existingUser) {
                    throw new \InvalidArgumentException('User with this email already exists.');
                }
            }
            $user->setEmail($dto->email);
        }

        if (!is_null($dto->role)) {
            $user->setRole($this->roleConverter->toSystemRole($dto->role));
        }

        return $user;
    }

    /**
     * Creates a new User entity from parameters.
     */
    public function createFromParameters(string $name, string $email, string $password, string $role): User
    {
        // Check if user with this email already exists
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            throw new \InvalidArgumentException('User with this email already exists.');
        }

        $user = new User($name, $email);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $role = $this->roleConverter->toSystemRole($role);

        $user->setPassword($hashedPassword);
        $user->setRole($role);

        return $user;
    }
}
