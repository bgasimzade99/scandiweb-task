<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

class Price
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByProductId(string $productId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM prices WHERE product_id = ? LIMIT 1"
        );
        $stmt->execute([$productId]);
        return $stmt->fetch() ?: null;
    }
}
