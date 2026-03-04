<?php

declare(strict_types=1);

namespace App\GraphQL\Schema;

use App\Service\OrderService;
use App\Service\OrderValidationException;
use GraphQL\Error\ClientAware;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Order mutation types and resolver.
 * Separated from SchemaBuilder for better separation of concerns.
 */
class OrderSchema
{
    public static function buildMutationField(OrderService $orderService): array
    {
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

        return [
            'placeOrder' => [
                'type' => $placeOrderPayload,
                'args' => [
                    'order' => ['type' => Type::nonNull($placeOrderInput)],
                ],
                'resolve' => static function ($root, array $args) use ($orderService): array {
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
        ];
    }
}
