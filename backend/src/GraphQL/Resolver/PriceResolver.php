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
        $price = $priceRepository->findByProductId($product['id'] ?? '');
        if (!$price) {
            return [];
        }
        return [[
            'amount' => (float) ($price['amount'] ?? 0),
            'currency' => [
                'label' => (string) ($price['currency_label'] ?? 'USD'),
                'symbol' => (string) ($price['currency_symbol'] ?? '$'),
            ],
        ]];
    }
}
