<?php

declare(strict_types=1);

namespace App\Script;

use App\Config\Database;

/**
 * Seeds MySQL database from Scandiweb-provided data.json.
 * Run: php -r "require 'vendor/autoload.php'; (new \App\Script\SeedFromDataJson())->run();"
 */
class SeedFromDataJson
{
    private \PDO $pdo;

    public function __construct()
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();
        $this->pdo = Database::getConnection();
    }

    public function run(): void
    {
        $baseDir = dirname(__DIR__, 2);
        $jsonPath = $baseDir . '/data.json';
        if (!file_exists($jsonPath)) {
            $jsonPath = dirname(__DIR__) . '/Controller/data.json';
        }
        if (!file_exists($jsonPath)) {
            throw new \RuntimeException('data.json not found. Place it in project root or src/Controller/');
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        $categories = $data['data']['categories'] ?? [];
        $products = $data['data']['products'] ?? [];

        $categoryMap = $this->seedCategories($categories);
        $this->seedProducts($products, $categoryMap);
    }

    private function seedCategories(array $categories): array
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->pdo->exec('DELETE FROM attribute_values');
        $this->pdo->exec('DELETE FROM attributes');
        $this->pdo->exec('DELETE FROM prices');
        $this->pdo->exec('DELETE FROM products');
        $this->pdo->exec('DELETE FROM orders');
        $this->pdo->exec('DELETE FROM categories');
        $this->pdo->exec('ALTER TABLE categories AUTO_INCREMENT = 1');
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        $map = [];
        $stmt = $this->pdo->prepare('INSERT INTO categories (id, name) VALUES (?, ?)');
        $id = 1;
        foreach ($categories as $cat) {
            $name = $cat['name'] ?? '';
            $stmt->execute([$id, $name]);
            $map[$name] = $id;
            $id++;
        }
        return $map;
    }

    private function seedProducts(array $products, array $categoryMap): void
    {
        $prodStmt = $this->pdo->prepare(
            'INSERT INTO products (id, name, in_stock, description, category_id, brand, gallery) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $priceStmt = $this->pdo->prepare(
            'INSERT INTO prices (amount, currency, product_id) VALUES (?, ?, ?)'
        );
        $attrStmt = $this->pdo->prepare(
            'INSERT INTO attributes (name, type, product_id) VALUES (?, ?, ?)'
        );
        $valStmt = $this->pdo->prepare(
            'INSERT INTO attribute_values (value, display_value, attribute_id) VALUES (?, ?, ?)'
        );

        foreach ($products as $p) {
            $categoryName = $p['category'] ?? 'all';
            $categoryId = $categoryMap[$categoryName] ?? 1;
            $inStock = ($p['inStock'] ?? true) ? 1 : 0;
            $gallery = json_encode($p['gallery'] ?? []);
            $description = $p['description'] ?? '';

            $prodStmt->execute([
                $p['id'],
                $p['name'],
                $inStock,
                $description,
                $categoryId,
                $p['brand'] ?? '',
                $gallery,
            ]);

            $price = $p['prices'][0] ?? null;
            if ($price) {
                $currency = json_encode($price['currency'] ?? ['label' => 'USD', 'symbol' => '$']);
                $priceStmt->execute([$price['amount'] ?? 0, $currency, $p['id']]);
            }

            foreach ($p['attributes'] ?? [] as $attr) {
                $attrStmt->execute([$attr['name'] ?? '', $attr['type'] ?? 'text', $p['id']]);
                $currentAttrId = (int) $this->pdo->lastInsertId();

                foreach ($attr['items'] ?? [] as $item) {
                    $display = $item['displayValue'] ?? $item['display_value'] ?? $item['value'] ?? '';
                    $valStmt->execute([
                        $item['value'] ?? '',
                        $display,
                        $currentAttrId,
                    ]);
                }
            }
        }
    }
}
