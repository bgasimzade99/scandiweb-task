<?php

/**
 * Migrates from JSON columns to plain VARCHAR/TEXT (company policy: no JSON in SQL).
 * Run: php scripts/migrate-remove-json.php
 *
 * - products.gallery: JSON -> TEXT (pipe-separated URLs)
 * - prices.currency: JSON -> currency_label, currency_symbol
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$pdo = \App\Config\Database::getConnection();
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

echo "Migrating: removing JSON columns...\n";

// 1. prices: add new columns, migrate, drop old
$stmt = $pdo->query("SHOW COLUMNS FROM prices LIKE 'currency'");
if ($stmt->rowCount() > 0) {
    $pdo->exec("ALTER TABLE prices ADD COLUMN currency_label VARCHAR(50) NOT NULL DEFAULT 'USD' AFTER amount");
    $pdo->exec("ALTER TABLE prices ADD COLUMN currency_symbol VARCHAR(10) NOT NULL DEFAULT '\$' AFTER currency_label");

    $rows = $pdo->query("SELECT id, currency FROM prices")->fetchAll(\PDO::FETCH_ASSOC);
    $updateStmt = $pdo->prepare("UPDATE prices SET currency_label = ?, currency_symbol = ? WHERE id = ?");
    foreach ($rows as $row) {
        $curr = $row['currency'];
        if (is_string($curr)) {
            $decoded = json_decode($curr, true);
            $curr = is_array($decoded) ? $decoded : [];
        } elseif (is_object($curr)) {
            $curr = (array) $curr;
        } else {
            $curr = [];
        }
        $label = (string) ($curr['label'] ?? 'USD');
        $symbol = (string) ($curr['symbol'] ?? '$');
        $updateStmt->execute([$label, $symbol, $row['id']]);
    }
    $pdo->exec("ALTER TABLE prices DROP COLUMN currency");
    echo "prices.currency -> currency_label, currency_symbol ✓\n";
}

// 2. products: change gallery JSON -> TEXT (pipe-separated)
$stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'gallery'");
if ($stmt->rowCount() > 0) {
    $col = $pdo->query("SHOW COLUMNS FROM products WHERE Field = 'gallery'")->fetch(\PDO::FETCH_ASSOC);
    if (isset($col['Type']) && stripos($col['Type'], 'json') !== false) {
        $rows = $pdo->query("SELECT id, gallery FROM products")->fetchAll(\PDO::FETCH_ASSOC);
        $updateStmt = $pdo->prepare("UPDATE products SET gallery = ? WHERE id = ?");
        foreach ($rows as $row) {
            $gallery = $row['gallery'];
            if (is_string($gallery)) {
                $arr = json_decode($gallery, true);
                $arr = is_array($arr) ? $arr : [];
            } elseif (is_array($gallery)) {
                $arr = $gallery;
            } else {
                $arr = [];
            }
            $urls = array_values(array_filter($arr, 'is_string'));
            $text = implode('|', $urls);
            $updateStmt->execute([$text, $row['id']]);
        }
        $pdo->exec("ALTER TABLE products MODIFY COLUMN gallery TEXT NOT NULL");
        echo "products.gallery: JSON -> TEXT ✓\n";
    }
}

echo "Done.\n";
