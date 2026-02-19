<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$host = $_ENV['DB_HOST'] ?? $_ENV['MYSQLHOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? $_ENV['MYSQLDATABASE'] ?? 'scandiweb';
$user = $_ENV['DB_USER'] ?? $_ENV['MYSQLUSER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? $_ENV['MYSQLPASSWORD'] ?? '';

// Create DB if not exists (connect without dbname first)
$dsn = "mysql:host={$host};charset=utf8mb4";
$pdoInit = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdoInit->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}`");
$pdoInit = null;

$pdo = \App\Config\Database::getConnection();
$pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);

$sql = file_get_contents(dirname(__DIR__) . '/scandiweb.sql');
$sql = preg_replace('/--.*$/m', '', $sql);
$sql = trim($sql);

$pdo->exec($sql);
echo "Schema and base data imported.\n";
