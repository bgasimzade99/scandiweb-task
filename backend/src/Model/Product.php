<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Product domain entity.
 * Implements EntityInterface via AbstractEntity.
 */
class Product extends AbstractEntity
{
    public function getId(): string|null
    {
        $id = $this->data['id'] ?? null;
        return $id !== null && $id !== '' ? (string) $id : null;
    }

    public function getName(): string
    {
        return (string) ($this->data['name'] ?? '');
    }

    public function isInStock(): bool
    {
        return (bool) ($this->data['in_stock'] ?? true);
    }

    public function getCategoryId(): int
    {
        return (int) ($this->data['category_id'] ?? 0);
    }

    public function getBrand(): string
    {
        return (string) ($this->data['brand'] ?? '');
    }

    public function getDescription(): string
    {
        return (string) ($this->data['description'] ?? '');
    }

    /**
     * Get gallery URLs as array (handles pipe-separated or JSON).
     *
     * @return list<string>
     */
    public function getGalleryUrls(): array
    {
        $raw = $this->data['gallery'] ?? '';
        if (is_array($raw)) {
            return array_values(array_filter($raw, 'is_string'));
        }
        if (!is_string($raw) || $raw === '') {
            return [];
        }
        $trimmed = trim($raw);
        if ($trimmed !== '' && ($trimmed[0] === '[' || $trimmed[0] === '{')) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
        }
        $urls = explode('|', $raw);
        return array_values(array_filter(array_map('trim', $urls), 'strlen'));
    }
}
