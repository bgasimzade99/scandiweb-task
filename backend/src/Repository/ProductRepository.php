<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Product;

class ProductRepository extends BaseRepository
{
    /**
     * @return list<Product>
     */
    public function findAll(): array
    {
        $rows = $this->fetchAll('SELECT * FROM products ORDER BY name');
        return Product::fromArrayList($rows);
    }

    public function findById(string $id): ?Product
    {
        $row = $this->fetchOne('SELECT * FROM products WHERE id = ?', [$id]);
        return $row !== null ? Product::fromArray($row) : null;
    }

    /**
     * @return list<Product>
     */
    public function findByCategory(int $categoryId): array
    {
        $rows = $this->fetchAll('SELECT * FROM products WHERE category_id = ? ORDER BY name', [$categoryId]);
        return Product::fromArrayList($rows);
    }

    public function hasAttributes(string $productId): bool
    {
        $row = $this->fetchOne('SELECT 1 FROM attributes WHERE product_id = ? LIMIT 1', [$productId]);
        return $row !== null;
    }
}
