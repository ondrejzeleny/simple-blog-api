<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for updating an existing article.
 */
class ArticleUpdateDto
{
    #[Assert\Length(max: 255)]
    public ?string $title = null;

    public ?string $content = null;

    /**
     * Create DTO.
     */
    public function __construct(?string $title, ?string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }
}
