<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$cfg = \App\Config\Database::getConfig();
$dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $cfg['host'], $cfg['port']);
$pdoInit = new PDO($dsn, $cfg['user'], $cfg['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdoInit->exec("CREATE DATABASE IF NOT EXISTS `{$cfg['dbname']}`");
$pdoInit = null;

$pdo = \App\Config\Database::getConnection();
$pdo->setAttribute(PDO::MYSQL_ATTR_MULTI_STATEMENTS, true);

$sql = file_get_contents(dirname(__DIR__) . '/scandiweb.sql');
$sql = preg_replace('/--.*$/m', '', $sql);
$sql = trim($sql);

$pdo->exec($sql);
echo "Schema and base data imported.\n";
