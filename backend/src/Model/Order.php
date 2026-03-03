<?php

declare(strict_types=1);

namespace App\Model;

use PDO;

class Order
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    /**
     * Creates an order with items and attributes in a single transaction.
     *
     * @param list<array{product_id: string, quantity: int, unit_price: float, attributes: list<array{name: string, value: string}>}> $items
     */
    public function create(array $items, float $total): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO orders (order_status, total, created_at) VALUES (?, ?, NOW())'
            );
            $stmt->execute(['received', $total]);
            $orderId = (int) $this->pdo->lastInsertId();

            $itemStmt = $this->pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)'
            );
            $attrStmt = $this->pdo->prepare(
                'INSERT INTO order_item_attributes (order_item_id, name, value) VALUES (?, ?, ?)'
            );

            foreach ($items as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['unit_price'],
                ]);
                $orderItemId = (int) $this->pdo->lastInsertId();

                foreach ($item['attributes'] ?? [] as $attr) {
                    $attrStmt->execute([
                        $orderItemId,
                        $attr['name'],
                        $attr['value'],
                    ]);
                }
            }

            $this->pdo->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
