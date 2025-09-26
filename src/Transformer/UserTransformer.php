<?php

namespace App\Transformer;

use App\Entity\User;
use App\Service\RoleConverter;

/**
 * Tranforms User object to array.
 */
class UserTransformer implements TransformerInterface
{
    public function __construct(
        public RoleConverter $roleConverter,
    ) {
    }

    /**
     * Transform User entity to array.
     *
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    public function transform(object $entity): array
    {
        if (!$entity instanceof User) {
            throw new \InvalidArgumentException('Entity is not instanceof '.User::class);
        }

        $prettyRole = $this->roleConverter->toPrettyRole($entity->getRole());

        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'email' => $entity->getEmail(),
            'role' => $prettyRole,
        ];
    }
}
