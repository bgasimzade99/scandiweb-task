<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Product;

class ProductRepository extends BaseRepository
{
    private const GALLERY_JOIN = 'LEFT JOIN product_gallery pg ON p.id = pg.product_id';
    private const GALLERY_SELECT = ', GROUP_CONCAT(pg.url ORDER BY pg.sort_order SEPARATOR \'|\') AS gallery';

    /**
     * @return list<Product>
     */
    public function findAll(): array
    {
        $rows = $this->fetchAll(
            'SELECT p.*' . self::GALLERY_SELECT . ' FROM products p ' . self::GALLERY_JOIN . ' GROUP BY p.id ORDER BY p.name'
        );
        return Product::fromArrayList($this->normalizeGalleryRows($rows));
    }

    public function findById(string $id): ?Product
    {
        $row = $this->fetchOne(
            'SELECT p.*' . self::GALLERY_SELECT . ' FROM products p ' . self::GALLERY_JOIN . ' WHERE p.id = ? GROUP BY p.id',
            [$id]
        );
        return $row !== null ? Product::fromArray($this->normalizeGalleryRow($row)) : null;
    }

    /**
     * @return list<Product>
     */
    public function findByCategory(int $categoryId): array
    {
        $rows = $this->fetchAll(
            'SELECT p.*' . self::GALLERY_SELECT . ' FROM products p ' . self::GALLERY_JOIN
            . ' WHERE p.category_id = ? GROUP BY p.id ORDER BY p.name',
            [$categoryId]
        );
        return Product::fromArrayList($this->normalizeGalleryRows($rows));
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeGalleryRow(array $row): array
    {
        $gallery = $row['gallery'] ?? null;
        $row['gallery'] = $gallery !== null && $gallery !== '' ? (string) $gallery : '';
        return $row;
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    private function normalizeGalleryRows(array $rows): array
    {
        return array_map([$this, 'normalizeGalleryRow'], $rows);
    }

    public function hasAttributes(string $productId): bool
    {
        $row = $this->fetchOne('SELECT 1 FROM attributes WHERE product_id = ? LIMIT 1', [$productId]);
        return $row !== null;
    }
}
