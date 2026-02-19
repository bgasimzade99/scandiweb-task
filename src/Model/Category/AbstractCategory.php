<?php

declare(strict_types=1);

namespace App\Model\Category;

use App\Model\AbstractModel;

abstract class AbstractCategory extends AbstractModel
{
    protected string $table = 'categories';

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY id");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByName(string $name): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE name = ?");
        $stmt->execute([$name]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
