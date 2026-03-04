<?php

declare(strict_types=1);

namespace App\Model;

use ArrayAccess;

/**
 * Base entity with common functionality: hydration, serialization, array access.
 * Reduces duplication across Product, Category, and future entities.
 */
abstract class AbstractEntity implements EntityInterface, ArrayAccess
{
    /** @var array<string, mixed> */
    protected array $data = [];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getId(): string|int|null
    {
        $id = $this->data['id'] ?? null;
        return $id !== null ? $id : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Get a value by key with optional default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new static($data);
    }

    /**
     * @param array<string, mixed> $rows
     * @return list<static>
     */
    public static function fromArrayList(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $result[] = static::fromArray($row);
            }
        }
        return $result;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset !== null) {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
