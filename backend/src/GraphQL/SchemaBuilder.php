<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\Config\Database;
use App\GraphQL\Schema\OrderSchema;
use App\GraphQL\Type\TypeRegistry;
use App\Model\Order;
use App\Repository\CategoryRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Service\OrderService;
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
                        return array_map(fn ($c) => $c->toArray(), $categoryRepository->findAll());
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
                        $category = $categoryRepository->findByName($title) ?? $categoryRepository->findByName('all');
                        return $category !== null ? $category->toArray() : null;
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
                        if ($category === null) {
                            return array_map(fn ($p) => $p->toArray(), $productRepository->findAll());
                        }
                        if ($category->getName() === 'all') {
                            return array_map(fn ($p) => $p->toArray(), $productRepository->findAll());
                        }
                        $products = $productRepository->findByCategory($category->getId() ?? 0);
                        return array_map(fn ($p) => $p->toArray(), $products);
                    },
                ],
                'product' => [
                    'type' => $productType,
                    'args' => [
                        'id' => ['type' => Type::nonNull(Type::string())],
                    ],
                    'resolve' => static function ($root, array $args) use ($productRepository) {
                        $product = $productRepository->findById($args['id']);
                        return $product !== null ? $product->toArray() : null;
                    },
                ],
            ],
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => OrderSchema::buildMutationField($orderService),
        ]);

        return new Schema(
            (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
        );
    }
}
