<?php

/**
 * Runs the orders normalization migration.
 * Replaces orders table with normalized schema (order_status, total, created_at).
 * Adds order_items and order_item_attributes. No JSON in orders.
 * Fresh installs use scandiweb.sql which already has the same schema.
 *
 * Run: composer migrate:orders
 * Or:  php scripts/migrate-orders-normalized.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$pdo = \App\Config\Database::getConnection();
$pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);

$sql = file_get_contents(__DIR__ . '/migrate-orders-normalized.sql');
$pdo->exec($sql);

echo "Orders migration completed.\n";
