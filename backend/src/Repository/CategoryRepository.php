<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Category;

class CategoryRepository extends BaseRepository
{
    /**
     * @return list<Category>
     */
    public function findAll(): array
    {
        $rows = $this->fetchAll('SELECT * FROM categories ORDER BY id');
        return Category::fromArrayList($rows);
    }

    public function findById(int $id): ?Category
    {
        $row = $this->fetchOne('SELECT * FROM categories WHERE id = ?', [$id]);
        return $row !== null ? Category::fromArray($row) : null;
    }

    public function findByName(string $name): ?Category
    {
        $row = $this->fetchOne('SELECT * FROM categories WHERE name = ?', [$name]);
        return $row !== null ? Category::fromArray($row) : null;
    }
}
