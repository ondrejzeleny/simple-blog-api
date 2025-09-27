<?php

namespace App\Tests\Unit;

use App\Exception\InvalidRoleException;
use App\Service\RoleConverter;
use PHPUnit\Framework\TestCase;

class RoleConverterTest extends TestCase
{
    private RoleConverter $roleConverter;

    protected function setUp(): void
    {
        $this->roleConverter = new RoleConverter();
    }

    public function testToSystemRoleWithValidRoles(): void
    {
        // Arrange
        $prettyRoles = [
            'admin' => 'ROLE_ADMIN',
            'author' => 'ROLE_AUTHOR',
            'reader' => 'ROLE_READER',
        ];

        foreach ($prettyRoles as $prettyRole => $expectedSystemRole) {
            // Act
            $result = $this->roleConverter->toSystemRole($prettyRole);
            // Assert
            $this->assertEquals($expectedSystemRole, $result, "Failed for role: $prettyRole");
        }
    }

    public function testToSystemRoleIsCaseInsensitive(): void
    {
        // Arrange
        $prettyRoles = [
            'ADMIN' => 'ROLE_ADMIN',
            'Author' => 'ROLE_AUTHOR',
            'ReAdeR' => 'ROLE_READER',
        ];

        foreach ($prettyRoles as $prettyRole => $expectedSystemRole) {
            // Act
            $result = $this->roleConverter->toSystemRole($prettyRole);
            // Assert
            $this->assertEquals($expectedSystemRole, $result, "Failed for role: $prettyRole");
        }
    }

    public function testToSystemRoleWithInvalidRole(): void
    {
        // Arrange
        $this->expectException(InvalidRoleException::class);
        $this->expectExceptionMessage('Role invalid_role not found.');
        $invalidRole = 'invalid_role';

        // Act
        $this->roleConverter->toSystemRole($invalidRole);

        // Assert - exception is thrown
    }

    public function testToPrettyRoleWithValidRoles(): void
    {
        // Arrange
        $systemRoles = [
            'ROLE_ADMIN' => 'admin',
            'ROLE_AUTHOR' => 'author',
            'ROLE_READER' => 'reader',
        ];

        foreach ($systemRoles as $systemRole => $expectedPrettyRole) {
            // Act
            $result = $this->roleConverter->toPrettyRole($systemRole);
            // Assert
            $this->assertEquals($expectedPrettyRole, $result, "Failed for role: $systemRole");
        }
    }

    public function testToPrettyRoleIsCaseInsensitive(): void
    {
        // Arrange
        $systemRoles = [
            'role_admin' => 'admin',
            'ROLE_author' => 'author',
            'role_READER' => 'reader',
        ];

        foreach ($systemRoles as $systemRole => $expectedPrettyRole) {
            // Act
            $result = $this->roleConverter->toPrettyRole($systemRole);
            // Assert
            $this->assertEquals($expectedPrettyRole, $result, "Failed for role: $systemRole");
        }
    }

    public function testToPrettyRoleWithInvalidRole(): void
    {
        // Arrange
        $this->expectException(InvalidRoleException::class);
        $this->expectExceptionMessage('Role ROLE_INVALID not found.');
        $invalidRole = 'ROLE_INVALID';

        // Act
        $this->roleConverter->toPrettyRole($invalidRole);

        // Assert - exception is thrown
    }
}
