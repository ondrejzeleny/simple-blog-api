<?php

namespace App\Transformer;

use App\Entity\Article;

/**
 * Tranforms Article object to array.
 */
class ArticleTransformer implements TransformerInterface
{
    /**
     * Transform Article entity to array.
     *
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    public function transform(object $entity): array
    {
        if (!$entity instanceof Article) {
            throw new \InvalidArgumentException('Entity is not instanceof '.Article::class);
        }

        return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle(),
            'content' => $entity->getContent(),
            'author_id' => $entity->getAuthor()->getId(),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
