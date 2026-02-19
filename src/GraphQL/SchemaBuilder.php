<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\Config\Database;
use App\GraphQL\Type\TypeRegistry;
use App\Model\Category\Category;
use App\Model\Order;
use App\Model\Product\Product;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

class SchemaBuilder
{
    public static function build(): Schema
    {
        $pdo = Database::getConnection();
        $categoryModel = Category::create($pdo);
        $productModel = Product::create($pdo);
        $orderModel = new Order($pdo);

        $categoryType = TypeRegistry::get('Category');
        $productType = TypeRegistry::get('Product');

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'categories' => [
                    'type' => Type::listOf($categoryType),
                    'resolve' => static function () use ($categoryModel) { return $categoryModel->findAll(); },
                ],
                'category' => [
                    'type' => $categoryType,
                    'args' => [
                        'input' => [
                            'type' => new InputObjectType([
                                'name' => 'CategoryInput',
                                'fields' => [
                                    'title' => ['type' => Type::string()],
                                ],
                            ]),
                        ],
                    ],
                    'resolve' => static function ($root, array $args) use ($categoryModel) {
                        $title = $args['input']['title'] ?? 'all';
                        $category = $categoryModel->findByName($title);
                        return $category ?: $categoryModel->findByName('all');
                    },
                ],
                'products' => [
                    'type' => Type::listOf($productType),
                    'args' => [
                        'category' => ['type' => Type::string()],
                    ],
                    'resolve' => static function ($root, array $args) use ($productModel, $categoryModel) {
                        $categoryTitle = $args['category'] ?? 'all';
                        $category = $categoryModel->findByName($categoryTitle);
                        if (!$category) {
                            return $productModel->findAll();
                        }
                        if ($category['name'] === 'all') {
                            return $productModel->findAll();
                        }
                        return $productModel->findByCategory((int) $category['id']);
                    },
                ],
                'product' => [
                    'type' => $productType,
                    'args' => [
                        'id' => ['type' => Type::nonNull(Type::string())],
                    ],
                    'resolve' => static function ($root, array $args) use ($productModel) {
                        return $productModel->findById($args['id']);
                    },
                ],
            ],
        ]);

        $orderProductInput = new InputObjectType([
            'name' => 'OrderProductInput',
            'fields' => [
                'id' => ['type' => Type::nonNull(Type::string())],
                'quantity' => ['type' => Type::int()],
                'attrs' => ['type' => Type::listOf(Type::string())],
            ],
        ]);

        $placeOrderInput = new InputObjectType([
            'name' => 'PlaceOrderInput',
            'fields' => [
                'products' => [
                    'type' => Type::nonNull(Type::listOf($orderProductInput)),
                ],
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'placeOrder' => [
                    'type' => Type::int(),
                    'args' => [
                        'order' => ['type' => Type::nonNull($placeOrderInput)],
                    ],
                    'resolve' => static function ($root, array $args) use ($orderModel, $productModel, $pdo) {
                        $productsInput = $args['order']['products'] ?? [];
                        $orderDetails = [];
                        $total = 0.0;

                        $priceStmt = $pdo->prepare(
                            'SELECT amount, currency FROM prices WHERE product_id = ? LIMIT 1'
                        );

                        foreach ($productsInput as $item) {
                            $product = $productModel->findById($item['id'] ?? '');
                            if (!$product) {
                                continue;
                            }
                            $priceStmt->execute([$product['id']]);
                            $price = $priceStmt->fetch();
                            $amount = $price ? (float) $price['amount'] : 0;
                            $currency = ['label' => 'USD', 'symbol' => '$'];
                            if ($price && isset($price['currency'])) {
                                $currency = is_string($price['currency'])
                                    ? json_decode($price['currency'], true)
                                    : $price['currency'];
                                $currency = is_array($currency) ? $currency : ['label' => 'USD', 'symbol' => '$'];
                            }
                            $qty = (int) ($item['quantity'] ?? 1);
                            $total += $amount * $qty;

                            $attrs = $item['attrs'] ?? [];
                            $orderDetails[] = [
                                'id' => $product['id'],
                                'name' => $product['name'],
                                'attrs' => $attrs,
                                'prices' => [['amount' => $amount, 'currency' => $currency]],
                                'quantity' => $qty,
                            ];
                        }

                        return $orderModel->create($orderDetails, $total);
                    },
                ],
            ],
        ]);

        return new Schema(
            (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
        );
    }
}
