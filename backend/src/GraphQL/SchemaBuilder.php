<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\Config\Database;
use App\GraphQL\Type\TypeRegistry;
use App\Model\Order;
use App\Repository\CategoryRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Service\OrderService;
use App\Service\OrderValidationException;
use GraphQL\Error\ClientAware;
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
        $categoryRepository = new CategoryRepository($pdo);
        $productRepository = new ProductRepository($pdo);
        $orderService = new OrderService(
            new Order($pdo),
            $productRepository,
            new PriceRepository($pdo),
        );

        $categoryType = TypeRegistry::get('Category');
        $productType = TypeRegistry::get('Product');

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'categories' => [
                    'type' => Type::listOf($categoryType),
                    'resolve' => static function () use ($categoryRepository) {
                        return $categoryRepository->findAll();
                    },
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
                    'resolve' => static function ($root, array $args) use ($categoryRepository) {
                        $title = $args['input']['title'] ?? 'all';
                        $category = $categoryRepository->findByName($title);
                        return $category ?: $categoryRepository->findByName('all');
                    },
                ],
                'products' => [
                    'type' => Type::listOf($productType),
                    'args' => [
                        'category' => ['type' => Type::string()],
                    ],
                    'resolve' => static function ($root, array $args) use ($productRepository, $categoryRepository) {
                        $categoryTitle = $args['category'] ?? 'all';
                        $category = $categoryRepository->findByName($categoryTitle);
                        if (!$category) {
                            return $productRepository->findAll();
                        }
                        if (($category['name'] ?? '') === 'all') {
                            return $productRepository->findAll();
                        }
                        return $productRepository->findByCategory((int) $category['id']);
                    },
                ],
                'product' => [
                    'type' => $productType,
                    'args' => [
                        'id' => ['type' => Type::nonNull(Type::string())],
                    ],
                    'resolve' => static function ($root, array $args) use ($productRepository) {
                        return $productRepository->findById($args['id']);
                    },
                ],
            ],
        ]);

        $orderProductAttrInput = new InputObjectType([
            'name' => 'OrderProductAttributeInput',
            'fields' => [
                'name' => ['type' => Type::nonNull(Type::string())],
                'value' => ['type' => Type::nonNull(Type::string())],
            ],
        ]);

        $orderProductInput = new InputObjectType([
            'name' => 'OrderProductInput',
            'fields' => [
                'id' => ['type' => Type::nonNull(Type::string())],
                'quantity' => ['type' => Type::int()],
                'attrs' => ['type' => Type::listOf($orderProductAttrInput)],
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

        $placeOrderPayload = new ObjectType([
            'name' => 'PlaceOrderPayload',
            'fields' => [
                'success' => ['type' => Type::nonNull(Type::boolean())],
                'orderId' => ['type' => Type::nonNull(Type::int())],
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'placeOrder' => [
                    'type' => $placeOrderPayload,
                    'args' => [
                        'order' => ['type' => Type::nonNull($placeOrderInput)],
                    ],
                    'resolve' => static function ($root, array $args) use ($orderService) {
                        $input = $args['order'] ?? null;
                        if (!is_array($input)) {
                            throw new OrderValidationException('Order input is required.');
                        }
                        if (!isset($input['products']) || !is_array($input['products'])) {
                            throw new OrderValidationException('Order products must be a non-empty array.');
                        }
                        try {
                            return $orderService->placeOrder($input);
                        } catch (\Throwable $e) {
                            if ($e instanceof ClientAware && $e->isClientSafe()) {
                                throw $e;
                            }
                            throw new OrderValidationException('Unable to place order. Please try again.');
                        }
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
