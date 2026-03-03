<?php

declare(strict_types=1);

namespace App\Model\Attribute;

use App\Model\AbstractModel;

abstract class AbstractAttribute extends AbstractModel
{
    abstract public static function getType(): string;

    /**
     * Format a raw attribute row into the standard item structure.
     * Subclasses may override for type-specific formatting.
     */
    public static function formatItem(array $row): array
    {
        return [
            'id' => $row['value_id'],
            'value' => $row['value'],
            'display_value' => $row['display_value'],
        ];
    }

    public function findByProductId(string $productId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT a.*, av.id as value_id, av.value, av.display_value
             FROM attributes a
             JOIN attribute_values av ON av.attribute_id = a.id
             WHERE a.product_id = ?
             ORDER BY a.id, av.id"
        );
        $stmt->execute([$productId]);

        $attributes = [];
        foreach ($stmt->fetchAll() as $row) {
            $attrId = $row['id'];
            if (!isset($attributes[$attrId])) {
                $attributes[$attrId] = [
                    'id' => (int) $row['id'],
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'items' => [],
                ];
            }
            $attributes[$attrId]['items'][] = [
                'id' => $row['value_id'],
                'value' => $row['value'],
                'display_value' => $row['display_value'],
            ];
        }

        return array_values($attributes);
    }
}
