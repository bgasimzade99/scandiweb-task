<?php

declare(strict_types=1);

namespace App\Repository;

class PriceRepository extends BaseRepository
{
    public function findByProductId(string $productId): ?array
    {
        return $this->fetchOne('SELECT * FROM prices WHERE product_id = ? LIMIT 1', [$productId]);
    }
}
