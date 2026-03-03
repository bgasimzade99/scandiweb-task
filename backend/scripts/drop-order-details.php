<?php

/**
 * Safely drops the legacy order_details column from orders if it exists.
 * Use for production DBs that had the old orders table with order_details JSON.
 *
 * Run: php scripts/drop-order-details.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$pdo = \App\Config\Database::getConnection();

$stmt = $pdo->query(
    "SELECT 1 FROM information_schema.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'order_details'"
);
$exists = $stmt && $stmt->fetch();

if ($exists) {
    $pdo->exec('ALTER TABLE `orders` DROP COLUMN `order_details`');
    echo "Dropped order_details column from orders.\n";
} else {
    echo "Column order_details does not exist in orders; nothing to do.\n";
}
