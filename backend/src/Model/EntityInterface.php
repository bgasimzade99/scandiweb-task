<?php

declare(strict_types=1);

namespace App\Model;

interface EntityInterface
{
    /**
     * Get the primary identifier of the entity.
     */
    public function getId(): string|int|null;

    /**
     * Convert entity to array for serialization (e.g. GraphQL, API).
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Create entity from raw data (e.g. database row).
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static;
}
