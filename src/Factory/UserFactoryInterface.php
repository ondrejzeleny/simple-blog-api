<?php

namespace App\Factory;

use App\Dto\UserCreateDto;
use App\Dto\UserUpdateDto;
use App\Entity\User;

/**
 * Interface for user factory.
 */
interface UserFactoryInterface
{
    /**
     * Create user from DTO.
     */
    public function createFromDto(UserCreateDto $dto): User;

    /**
     * Update user from DTO.
     */
    public function updateFromDto(User $user, UserUpdateDto $dto): User;

    /**
     * Create from parameters.
     */
    public function createFromParameters(string $name, string $email, string $password, string $role): User;
}
