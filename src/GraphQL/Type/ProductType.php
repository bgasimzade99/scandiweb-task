<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\GraphQL\Resolver\AttributeResolver as AttributeResolverClass;
use App\GraphQL\Resolver\PriceResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType
{
    public function __construct(?callable $attributeResolver = null, ?callable $priceResolver = null)
    {
        $attrResolver = $attributeResolver ?? new AttributeResolverClass();
        $priceRes = $priceResolver ?? new PriceResolver();

        parent::__construct([
            'name' => 'Product',
            'fields' => [
                'id' => ['type' => Type::string()],
                'name' => ['type' => Type::string()],
                'in_stock' => [
                    'type' => Type::boolean(),
                    'resolve' => static function (array $product) {
                        return (bool) ($product['in_stock'] ?? true);
                    },
                ],
                'brand' => ['type' => Type::string()],
                'description' => ['type' => Type::string()],
                'gallery' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => static function (array $product) {
                        $gallery = $product['gallery'] ?? '[]';
                        $decoded = is_string($gallery) ? (json_decode($gallery, true) ?? []) : ($gallery ?? []);
                        return is_array($decoded) ? $decoded : [];
                    },
                ],
                'prices' => [
                    'type' => Type::listOf(TypeRegistry::get('Price')),
                    'resolve' => $priceRes,
                ],
                'attributes' => [
                    'type' => Type::listOf(TypeRegistry::get('Attribute')),
                    'resolve' => $attrResolver,
                ],
            ],
        ]);
    }
}
