<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for creating a new user.
 */
class UserCreateDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public readonly string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public readonly string $password;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly string $name;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['admin', 'author', 'reader'], message: 'Choose a valid role: admin, author, or reader.')]
    public readonly string $role;

    public function __construct(string $email, string $password, string $name, string $role)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->role = $role;
    }
}
