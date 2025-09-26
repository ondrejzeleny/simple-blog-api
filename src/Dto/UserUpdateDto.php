<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for updating an existing user.
 */
class UserUpdateDto
{
    #[Assert\NotBlank(message: 'Email cannot be blank.')]
    #[Assert\Email(message: 'Email is not valid.')]
    public readonly string $email;

    #[Assert\NotBlank(message: 'Name cannot be blank.')]
    #[Assert\Length(max: 255)]
    public readonly string $name;

    #[Assert\NotBlank(message: 'Role cannot be blank.')]
    #[Assert\Choice(choices: ['admin', 'author', 'reader'], message: 'Choose a valid role: admin, author, or reader.')]
    public readonly string $role;

    public function __construct(string $email, string $name, string $role)
    {
        $this->email = $email;
        $this->name = $name;
        $this->role = $role;
    }
}
