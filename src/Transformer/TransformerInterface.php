<?php

namespace App\Transformer;

/**
 * Interface for classes transforming object to array.
 */
interface TransformerInterface
{
    /**
     * Transform entity to array.
     *
     * @return array<string, mixed>
     */
    public function transform(object $entity): array;
}
