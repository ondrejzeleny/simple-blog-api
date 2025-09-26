<?php

namespace App\Service;

use App\Exception\InvalidRoleException;

class RoleConverter
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_AUTHOR = 'ROLE_AUTHOR';
    public const ROLE_READER = 'ROLE_READER';

    public function toSystemRole(string $prettyRole): string
    {
        if (!in_array($prettyRole, ['admin', 'author', 'reader'])) {
            throw new InvalidRoleException("Role {$prettyRole} not found.");
        }

        return match (strtolower($prettyRole)) {
            'admin' => self::ROLE_ADMIN,
            'author' => self::ROLE_AUTHOR,
            'reader' => self::ROLE_READER,
        };
    }

    public function toPrettyRole(string $systemRole): string
    {
        if (!in_array($systemRole, [self::ROLE_ADMIN, self::ROLE_AUTHOR, self::ROLE_READER])) {
            throw new InvalidRoleException("Role {$systemRole} not found.");
        }

        return match (strtoupper($systemRole)) {
            self::ROLE_ADMIN => 'admin',
            self::ROLE_AUTHOR => 'author',
            self::ROLE_READER => 'reader',
        };
    }
}
