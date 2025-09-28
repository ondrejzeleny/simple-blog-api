<?php

namespace App\Service;

use App\Exception\InvalidRoleException;

/**
 * Service for converting between role formats.
 */
class RoleConverter
{
    private const ROLE_ADMIN = 'ROLE_ADMIN';
    private const ROLE_AUTHOR = 'ROLE_AUTHOR';
    private const ROLE_READER = 'ROLE_READER';

    /**
     * Convert pretty role to system role.
     */
    public function toSystemRole(string $prettyRole): string
    {
        $prettyRole = strtolower($prettyRole);

        if (!in_array($prettyRole, ['admin', 'author', 'reader'])) {
            throw new InvalidRoleException("Role {$prettyRole} not found.");
        }

        return match ($prettyRole) {
            'admin' => self::ROLE_ADMIN,
            'author' => self::ROLE_AUTHOR,
            'reader' => self::ROLE_READER,
        };
    }

    /**
     * Convert system role to pretty role.
     */
    public function toPrettyRole(string $systemRole): string
    {
        $systemRole = strtoupper($systemRole);

        if (!in_array($systemRole, [self::ROLE_ADMIN, self::ROLE_AUTHOR, self::ROLE_READER])) {
            throw new InvalidRoleException("Role {$systemRole} not found.");
        }

        return match ($systemRole) {
            self::ROLE_ADMIN => 'admin',
            self::ROLE_AUTHOR => 'author',
            self::ROLE_READER => 'reader',
        };
    }
}
