<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

class Order
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $orderDetails, float $total): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO orders (order_details, order_status, total, created_at) VALUES (?, 'received', ?, NOW())"
        );
        $stmt->execute([json_encode($orderDetails), $total]);
        return (int) $this->pdo->lastInsertId();
    }
}
