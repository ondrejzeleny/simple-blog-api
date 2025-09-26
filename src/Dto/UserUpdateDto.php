<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for updating an existing user.
 */
class UserUpdateDto
{
    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Email(message: 'Email is not valid.'),
    ])]
    public ?string $email = null;

    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Choice(choices: ['admin', 'author', 'reader'], message: 'Choose a valid role: admin, author, or reader.'),
    ])]
    public ?string $role = null;

    /**
     * Create DTO.
     */
    public function __construct(?string $email, ?string $name, ?string $role)
    {
        $this->email = $email;
        $this->name = $name;
        $this->role = $role;
    }
}
