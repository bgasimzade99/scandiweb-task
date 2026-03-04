<?php

/**
 * Migrates products.gallery (TEXT, pipe-separated) to product_gallery table.
 * Run: php scripts/migrate-gallery-to-table.php
 *
 * Idempotent: skips if product_gallery already has data.
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$pdo = \App\Config\Database::getConnection();

// Check if product_gallery exists and has rows
$tables = $pdo->query("SHOW TABLES LIKE 'product_gallery'")->fetchAll();
if (empty($tables)) {
    $pdo->exec("
        CREATE TABLE product_gallery (
            id bigint NOT NULL AUTO_INCREMENT,
            product_id varchar(255) NOT NULL,
            url varchar(1024) NOT NULL,
            sort_order int NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            KEY product_gallery_products_id_fk (product_id),
            CONSTRAINT product_gallery_products_id_fk FOREIGN KEY (product_id)
                REFERENCES products (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "Created product_gallery table.\n";
}

$count = (int) $pdo->query('SELECT COUNT(*) FROM product_gallery')->fetchColumn();
if ($count > 0) {
    echo "product_gallery already has data. Skipping migration.\n";
    exit(0);
}

$hasGallery = $pdo->query("SHOW COLUMNS FROM products LIKE 'gallery'")->rowCount() > 0;
if (!$hasGallery) {
    echo "products.gallery column not found. Nothing to migrate.\n";
    exit(0);
}

$stmt = $pdo->query('SELECT id, gallery FROM products WHERE gallery IS NOT NULL AND gallery != ""');
$insert = $pdo->prepare('INSERT INTO product_gallery (product_id, url, sort_order) VALUES (?, ?, ?)');
$migrated = 0;

while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $urls = array_filter(array_map('trim', explode('|', $row['gallery'])), 'strlen');
    foreach ($urls as $i => $url) {
        $insert->execute([$row['id'], $url, $i]);
        $migrated++;
    }
}

echo "Migrated {$migrated} gallery URLs into product_gallery.\n";

$pdo->exec('ALTER TABLE products DROP COLUMN gallery');
echo "Dropped products.gallery column.\n";
