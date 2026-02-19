<?php

declare(strict_types=1);

namespace App\Config;

use PDO;

class Database
{
    private static ?PDO $instance = null;

    /** @return array{host:string,port:int,dbname:string,user:string,pass:string} */
    public static function getConfig(): array
    {
        $host = $_ENV['MYSQLHOST'] ?? $_ENV['DB_HOST'] ?? 'localhost';
        $port = (int) ($_ENV['MYSQLPORT'] ?? $_ENV['DB_PORT'] ?? '3306');
        $dbname = $_ENV['MYSQLDATABASE'] ?? $_ENV['DB_NAME'] ?? 'scandiweb';
        $user = $_ENV['MYSQLUSER'] ?? $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['MYSQLPASSWORD'] ?? $_ENV['DB_PASS'] ?? '';

        $host = self::normalizeHost($host);

        return ['host' => $host, 'port' => $port, 'dbname' => $dbname, 'user' => $user, 'pass' => $pass];
    }

    private static function normalizeHost(string $host): string
    {
        $host = trim($host);
        if ($host === '') {
            return 'localhost';
        }
        if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $host)) {
            $parsed = parse_url($host);
            $host = $parsed['host'] ?? 'localhost';
        }
        if (strpos($host, ':') !== false) {
            $host = explode(':', $host, 2)[0];
        }
        return $host;
    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $cfg = self::getConfig();
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $cfg['host'],
                $cfg['port'],
                $cfg['dbname']
            );
            self::$instance = new PDO($dsn, $cfg['user'], $cfg['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$instance;
    }

    public static function testConnection(): bool
    {
        try {
            self::getConnection();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** @return array{host:string,port:int,user:string,password_masked:string} */
    public static function getConfigForHealth(): array
    {
        $cfg = self::getConfig();
        return [
            'host' => $cfg['host'],
            'port' => $cfg['port'],
            'user' => $cfg['user'],
            'password_masked' => $cfg['pass'] !== '' ? '***' : '(empty)',
        ];
    }
}
