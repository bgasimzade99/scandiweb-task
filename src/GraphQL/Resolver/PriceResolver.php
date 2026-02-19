<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Model\Price;
use App\Config\Database;

class PriceResolver
{
    public function __invoke(array $product, array $args = []): array
    {
        $priceModel = new Price(Database::getConnection());
        $price = $priceModel->findByProductId($product['id']);
        if (!$price) {
            return [];
        }
        $currency = is_string($price['currency']) 
            ? json_decode($price['currency'], true) 
            : $price['currency'];
        return [[
            'amount' => (float) $price['amount'],
            'currency' => $currency,
        ]];
    }
}
