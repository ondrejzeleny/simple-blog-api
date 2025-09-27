<?php

namespace App\Factory;

use App\Dto\ArticleCreateDto;
use App\Dto\ArticleUpdateDto;
use App\Entity\Article;
use App\Entity\User;

/**
 * Interface for article factory.
 */
interface ArticleFactoryInterface
{
    /**
     * Create article from DTO.
     */
    public function createFromDto(ArticleCreateDto $dto, User $author): Article;

    /**
     * Update article from DTO.
     */
    public function updateFromDto(Article $article, ArticleUpdateDto $dto): Article;

    /**
     * Create from parameters.
     */
    public function createFromParameters(string $title, string $content, User $author): Article;
}
