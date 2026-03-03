<?php

declare(strict_types=1);

namespace App\Repository;

class CategoryRepository extends BaseRepository
{
    public function findAll(): array
    {
        return $this->fetchAll('SELECT * FROM categories ORDER BY id');
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    public function findByName(string $name): ?array
    {
        return $this->fetchOne('SELECT * FROM categories WHERE name = ?', [$name]);
    }
}
