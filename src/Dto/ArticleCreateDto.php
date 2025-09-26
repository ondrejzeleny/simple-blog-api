<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for creating a new article.
 */
class ArticleCreateDto
{
    #[Assert\NotBlank(message: 'Title cannot be blank.')]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank(message: 'Content cannot be blank.')]
    public string $content;

    /**
     * Create DTO.
     */
    public function __construct(string $title, string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }
}
