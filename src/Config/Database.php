<?php

declare(strict_types=1);

namespace App\Config;

use PDO;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? $_ENV['MYSQLHOST'] ?? 'localhost';
            $dbname = $_ENV['DB_NAME'] ?? $_ENV['MYSQLDATABASE'] ?? 'scandiweb';
            $user = $_ENV['DB_USER'] ?? $_ENV['MYSQLUSER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? $_ENV['MYSQLPASSWORD'] ?? '';
            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$instance;
    }
}
