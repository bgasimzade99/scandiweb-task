<?php

/**
 * Ensures products table has gallery column (TEXT, pipe-separated URLs).
 * Run: php scripts/migrate-gallery.php
 */
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$pdo = \App\Config\Database::getConnection();

$stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'gallery'");
if ($stmt->rowCount() === 0) {
    $pdo->exec('ALTER TABLE products ADD COLUMN gallery TEXT NOT NULL AFTER brand');
    echo "Added gallery column to products table.\n";
} else {
    echo "Gallery column already exists.\n";
}
