<?php

declare(strict_types=1);

namespace App\Model\Product;

use App\Model\AbstractModel;

abstract class AbstractProduct extends AbstractModel
{
    protected string $table = 'products';

    public function findByCategory(int $categoryId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE category_id = ? ORDER BY name"
        );
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY name");
        return $stmt->fetchAll();
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
