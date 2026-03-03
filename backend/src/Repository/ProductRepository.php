<?php

declare(strict_types=1);

namespace App\Repository;

class ProductRepository extends BaseRepository
{
    public function findAll(): array
    {
        return $this->fetchAll('SELECT * FROM products ORDER BY name');
    }

    public function findById(string $id): ?array
    {
        return $this->fetchOne('SELECT * FROM products WHERE id = ?', [$id]);
    }

    public function findByCategory(int $categoryId): array
    {
        return $this->fetchAll('SELECT * FROM products WHERE category_id = ? ORDER BY name', [$categoryId]);
    }

    public function hasAttributes(string $productId): bool
    {
        $row = $this->fetchOne('SELECT 1 FROM attributes WHERE product_id = ? LIMIT 1', [$productId]);
        return $row !== null;
    }
}
