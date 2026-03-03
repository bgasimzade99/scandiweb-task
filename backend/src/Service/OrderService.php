<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Order;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;

/**
 * Handles order business logic: validate products, resolve prices, build order items, persist via Order model.
 */
class OrderService
{
    public function __construct(
        private Order $orderModel,
        private ProductRepository $productRepository,
        private PriceRepository $priceRepository,
    ) {
    }

    /**
     * @param array{products: array<int, array{id: string, quantity?: int, attrs?: array<int, array{name: string, value: string}>}>} $input
     * @return array{success: bool, orderId: int}
     */
    public function placeOrder(array $input): array
    {
        $productsInput = $input['products'] ?? [];
        if (count($productsInput) === 0) {
            throw new OrderValidationException('Order must contain at least one product.');
        }

        $total = 0.0;
        $items = [];

        foreach ($productsInput as $idx => $item) {
            $productId = $item['id'] ?? '';
            if ($productId === '') {
                throw new OrderValidationException("Product at index {$idx} has no id.");
            }

            $product = $this->productRepository->findById($productId);
            if (!$product) {
                throw new OrderValidationException("Product '{$productId}' not found.");
            }

            $priceRow = $this->priceRepository->findByProductId($productId);
            if (!$priceRow || !isset($priceRow['amount'])) {
                throw new OrderValidationException("Product '{$productId}' has no price.");
            }
            $unitPrice = (float) $priceRow['amount'];

            $qty = (int) ($item['quantity'] ?? 1);
            if ($qty <= 0) {
                throw new OrderValidationException("Product '{$productId}' quantity must be greater than 0.");
            }

            $attrs = $this->extractAttributes($item['attrs'] ?? []);

            if ($this->productRepository->hasAttributes($productId) && count($attrs) === 0) {
                throw new OrderValidationException("Product '{$productId}' requires attribute selection.");
            }

            $total += $unitPrice * $qty;
            $items[] = [
                'product_id' => $productId,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'attributes' => $attrs,
            ];
        }

        $orderId = $this->orderModel->create($items, $total);

        return ['success' => true, 'orderId' => $orderId];
    }

    /**
     * @param array<int, mixed> $rawAttrs
     * @return list<array{name: string, value: string}>
     */
    private function extractAttributes(array $rawAttrs): array
    {
        $attrs = [];
        foreach ($rawAttrs as $a) {
            if (!is_array($a)) {
                continue;
            }
            $name = $a['name'] ?? '';
            $value = $a['value'] ?? '';
            if ($name !== '') {
                $attrs[] = ['name' => $name, 'value' => $value];
            }
        }
        return $attrs;
    }
}
