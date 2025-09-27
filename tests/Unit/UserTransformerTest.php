<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Service\RoleConverter;
use App\Transformer\UserTransformer;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for UserTransformer.
 */
class UserTransformerTest extends TestCase
{
    private UserTransformer $transformer;

    protected function setUp(): void
    {
        $roleConverter = new RoleConverter();
        $this->transformer = new UserTransformer($roleConverter);
    }

    /**
     * Test transforming user to array.
     */
    public function testTransformUser(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('getName')->willReturn('Test User');
        $user->method('getEmail')->willReturn('user@test.com');
        $user->method('getRole')->willReturn('ROLE_AUTHOR');

        $expectedArray = [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'user@test.com',
            'role' => 'author',
        ];

        // Act
        $result = $this->transformer->transform($user);

        // Assert
        $this->assertEquals($expectedArray, $result);
    }

    /**
     * Test transforming user with admin role.
     */
    public function testTransformUserWithAdminRole(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn('ROLE_ADMIN');

        // Act
        $result = $this->transformer->transform($user);

        // Assert
        $this->assertEquals('admin', $result['role']);
    }

    /**
     * Test transforming user with reader role.
     */
    public function testTransformUserWithReaderRole(): void
    {
        // Arrange
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn('ROLE_READER');

        // Act
        $result = $this->transformer->transform($user);

        // Assert
        $this->assertEquals('reader', $result['role']);
    }

    /**
     * Test transforming invalid entity throws exception.
     */
    public function testTransformWithInvalidEntity(): void
    {
        // Arrange
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity is not instanceof App\Entity\User');
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
