<?php

namespace App\Factory;

use App\Dto\ArticleCreateDto;
use App\Dto\ArticleUpdateDto;
use App\Entity\Article;
use App\Entity\User;

/**
 * Factory for creating and updating Article entities.
 */
class ArticleFactory implements ArticleFactoryInterface
{
    /**
     * Creates a new Article entity from a DTO and an author.
     */
    public function createFromDto(ArticleCreateDto $dto, User $author): Article
    {
        $article = new Article($dto->title, $author);
        $article->setContent($dto->content);

        return $article;
    }

    /**
     * Updates an existing Article entity from a DTO.
     */
    public function updateFromDto(Article $article, ArticleUpdateDto $dto): Article
    {
        $isUpdated = false;

        if (!is_null($dto->title)) {
            $article->setTitle($dto->title);
            $isUpdated = true;
        }

        if (!is_null($dto->content)) {
            $article->setContent($dto->content);
            $isUpdated = true;
        }

        if ($isUpdated) {
            $article->setUpdatedAt(new \DateTimeImmutable());
        }

        return $article;
    }

    /**
     * Creates a new Article entity from parameters.
     */
    public function createFromParameters(string $title, string $content, User $author): Article
    {
        $article = new Article($title, $author);
        $article->setContent($content);

        return $article;
    }
}
