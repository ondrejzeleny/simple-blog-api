<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

/**
 * DTO for creating a new user.
 */
class UserCreateDto
{
    #[Assert\NotBlank(message: 'Email cannot be blank.')]
    #[Assert\Email(message: 'Email is not valid.')]
    public string $email;

    #[Assert\NotBlank(message: 'Password cannot be blank.')]
    #[PasswordStrength(minScore: PasswordStrength::STRENGTH_WEAK, message: 'Use a stronger password.')]
    public string $password;

    #[Assert\NotBlank(message: 'Name cannot be blank.')]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\NotBlank(message: 'Role cannot be blank.')]
    #[Assert\Choice(choices: ['admin', 'author', 'reader'], message: 'Choose a valid role: admin, author, or reader.')]
    public string $role;

    public function __construct(string $email, string $password, string $name, string $role)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->role = $role;
    }
}
