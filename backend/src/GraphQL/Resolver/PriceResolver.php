<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Config\Database;
use App\Repository\PriceRepository;

class PriceResolver
{
    public function __invoke(array $product, array $args = []): array
    {
        $priceRepository = new PriceRepository(Database::getConnection());
        $price = $priceRepository->findByProductId($product['id']);
        if (!$price) {
            return [];
        }
        $currency = is_string($price['currency'] ?? '') 
            ? (json_decode($price['currency'], true) ?: ['label' => 'USD', 'symbol' => '$']) 
            : ($price['currency'] ?? ['label' => 'USD', 'symbol' => '$']);
        if (!is_array($currency)) {
            $currency = ['label' => 'USD', 'symbol' => '$'];
        }
        return [[
            'amount' => (float) $price['amount'],
            'currency' => $currency,
        ]];
    }
}
