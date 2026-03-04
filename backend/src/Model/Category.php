<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Category domain entity.
 * Implements EntityInterface via AbstractEntity.
 */
class Category extends AbstractEntity
{
    public function getId(): int|null
    {
        $id = $this->data['id'] ?? null;
        return $id !== null ? (int) $id : null;
    }

    public function getName(): string
    {
        return (string) ($this->data['name'] ?? '');
    }
}
