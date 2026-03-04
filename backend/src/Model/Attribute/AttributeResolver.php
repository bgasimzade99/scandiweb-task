<?php

declare(strict_types=1);

namespace App\Model\Attribute;

use App\Service\AttributeFormatter;
use PDO;

class AttributeResolver
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function getAttributesForProduct(string $productId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT a.*, av.id as value_id, av.value, av.display_value
             FROM attributes a
             JOIN attribute_values av ON av.attribute_id = a.id
             WHERE a.product_id = ?
             ORDER BY a.id, av.id"
        );
        $stmt->execute([$productId]);

        $grouped = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $attrId = $row['id'];
            $type = $row['type'] ?? 'text';

            if (!isset($grouped[$attrId])) {
                $grouped[$attrId] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'type' => $type,
                    'items' => [],
                ];
            }
            $grouped[$attrId]['items'][] = AttributeFormatter::formatItem($row);
        }

        return array_values($grouped);
    }
}
