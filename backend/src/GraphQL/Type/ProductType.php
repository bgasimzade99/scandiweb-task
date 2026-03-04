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
                        $raw = $product['gallery'] ?? '';
                        if (is_array($raw)) {
                            $urls = array_values(array_filter($raw, 'is_string'));
                        } elseif (is_string($raw) && $raw !== '') {
                            $trimmed = trim($raw);
                            if ($trimmed !== '' && ($trimmed[0] === '[' || $trimmed[0] === '{')) {
                                $decoded = json_decode($raw, true);
                                $urls = is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
                            } else {
                                $urls = explode('|', $raw);
                            }
                        } else {
                            $urls = [];
                        }
                        return array_values(array_filter(array_map('trim', $urls), 'strlen'));
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
