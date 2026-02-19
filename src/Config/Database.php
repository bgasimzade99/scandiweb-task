<?php

declare(strict_types=1);

namespace App\Config;

use PDO;

class Database
{
    private static ?PDO $instance = null;

    private static function env(string $key): ?string
    {
        $v = $_ENV[$key] ?? null;
        if ($v !== null && $v !== '') {
            return self::rejectUnexpanded($v) ? null : $v;
        }
        $v = getenv($key);
        if ($v === false || $v === '') {
            return null;
        }
        return self::rejectUnexpanded($v) ? null : $v;
    }

    /** Reject unexpanded template vars (e.g. ${VAR}, ${{Service.VAR}}) - treat as invalid */
    private static function rejectUnexpanded(string $val): bool
    {
        return strpos($val, '${') !== false;
    }

    /** @return array{host:string,port:int,dbname:string,user:string,pass:string}|null */
    private static function parseMysqlUrl(string $url): ?array
    {
        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['host']) || ($parsed['scheme'] ?? '') !== 'mysql') {
            return null;
        }
        $host = $parsed['host'];
        $port = isset($parsed['port']) ? (int) $parsed['port'] : 3306;
        $dbname = isset($parsed['path']) ? trim($parsed['path'], '/') : 'scandiweb';
        $user = $parsed['user'] ?? 'root';
        $pass = $parsed['pass'] ?? '';
        if (self::rejectUnexpanded($url) || self::rejectUnexpanded($pass)) {
            return null;
        }
        return ['host' => $host, 'port' => $port, 'dbname' => $dbname, 'user' => $user, 'pass' => $pass];
    }

    /** @return array{host:string,port:int,dbname:string,user:string,pass:string} */
    public static function getConfig(): array
    {
        $url = self::env('MYSQL_PUBLIC_URL') ?? self::env('DATABASE_URL');
        if ($url !== null) {
            $parsed = self::parseMysqlUrl($url);
            if ($parsed !== null) {
                return $parsed;
            }
        }

        $host = self::env('MYSQLHOST') ?? self::env('DB_HOST') ?? 'localhost';
        $port = (int) (self::env('MYSQLPORT') ?? self::env('DB_PORT') ?? '3306');
        $dbname = self::env('MYSQLDATABASE') ?? self::env('DB_NAME') ?? 'scandiweb';
        $user = self::env('MYSQLUSER') ?? self::env('DB_USER') ?? 'root';
        $pass = self::env('MYSQLPASSWORD') ?? self::env('DB_PASS') ?? '';

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

    /** @return array{host:string,port:int,user:string,password_masked:string,hostUsed:string,portUsed:int,envSnapshotMasked:array} */
    public static function getConfigForHealth(): array
    {
        $cfg = self::getConfig();
        $url = self::env('MYSQL_PUBLIC_URL') ?? self::env('DATABASE_URL');
        $mh = self::env('MYSQLHOST');
        $mp = self::env('MYSQLPORT');
        $dh = self::env('DB_HOST');
        $dp = self::env('DB_PORT');
        $envSnapshot = [
            'MYSQL_PUBLIC_URL' => $url !== null ? '(set)' : '(not set)',
            'MYSQLHOST' => $mh !== null ? $mh : '(not set)',
            'MYSQLPORT' => $mp !== null ? (string) $mp : '(not set)',
            'DB_HOST' => $dh !== null ? $dh : '(not set)',
            'DB_PORT' => $dp !== null ? (string) $dp : '(not set)',
        ];
        return [
            'host' => $cfg['host'],
            'port' => $cfg['port'],
            'user' => $cfg['user'],
            'password_masked' => $cfg['pass'] !== '' ? '***' : '(empty)',
            'hostUsed' => $cfg['host'],
            'portUsed' => $cfg['port'],
            'envSnapshotMasked' => $envSnapshot,
        ];
    }
}
