<?php

declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Model\Attribute\AttributeResolver as AttributeModelResolver;
use App\Config\Database;

class AttributeResolver
{
    public function __invoke(array $product, array $args = []): array
    {
        $modelResolver = new AttributeModelResolver(Database::getConnection());
        return $modelResolver->getAttributesForProduct($product['id']);
    }
}
