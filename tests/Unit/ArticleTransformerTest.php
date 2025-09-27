<?php

namespace App\Tests\Unit;

use App\Entity\Article;
use App\Entity\User;
use App\Transformer\ArticleTransformer;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for ArticleTransformer.
 */
class ArticleTransformerTest extends TestCase
{
    private ArticleTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new ArticleTransformer();
    }

    /**
     * Test transforming article to array.
     */
    public function testTransformArticle(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);

        $article = $this->createMock(Article::class);
        $article->method('getId')->willReturn(1);
        $article->method('getTitle')->willReturn('Test Article');
        $article->method('getContent')->willReturn('Test content');
        $article->method('getAuthor')->willReturn($user);
        $article->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2024-01-01 12:00:00'));
        $article->method('getUpdatedAt')->willReturn(new \DateTimeImmutable('2024-01-01 12:00:00'));

        $expectedArray = [
            'id' => 1,
            'title' => 'Test Article',
            'content' => 'Test content',
            'author_id' => 1,
            'created_at' => '2024-01-01 12:00:00',
            'updated_at' => '2024-01-01 12:00:00',
        ];

        // Act
        $result = $this->transformer->transform($article);

        // Assert
        $this->assertEquals($expectedArray, $result);
    }

    /**
     * Test transforming article with null updated_at.
     */
    public function testTransformArticleWithNullUpdatedAt(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);

        $article = $this->createMock(Article::class);
        $article->method('getId')->willReturn(1);
        $article->method('getTitle')->willReturn('Test Article');
        $article->method('getContent')->willReturn('Test content');
        $article->method('getAuthor')->willReturn($user);
        $article->method('getCreatedAt')->willReturn(new \DateTimeImmutable('2024-01-01 12:00:00'));
        $article->method('getUpdatedAt')->willReturn(null);

        // Act
        $result = $this->transformer->transform($article);

        // Assert
        $this->assertNull($result['updated_at']);
    }

    /**
     * Test transforming invalid entity throws exception.
     */
    public function testTransformWithInvalidEntity(): void
    {
        // Arrange
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity is not instanceof App\Entity\Article');
        $invalidEntity = new \stdClass();

        // Act
        $this->transformer->transform($invalidEntity);

        // Assert - exception is thrown
    }

    /**
     * Test transforming null entity throws exception.
     */
    public function testTransformWithNullEntity(): void
    {
        // Arrange
        $this->expectException(\TypeError::class);
        /** @var mixed $null */
        $null = null;

        // Act
        $this->transformer->transform($null);

        // Assert - exception is thrown
    }
}
