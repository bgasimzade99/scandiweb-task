<?php

/**
 * Runs the orders normalization migration.
 * Use for existing DBs that have the old orders table with order_details JSON.
 * Fresh installs use scandiweb.sql which already has the new schema.
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
