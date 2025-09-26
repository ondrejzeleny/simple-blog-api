<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for updating an existing article.
 */
class ArticleUpdateDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public readonly string $title;

    #[Assert\NotBlank]
    public readonly string $content;

    public function __construct(string $title, string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }
}
