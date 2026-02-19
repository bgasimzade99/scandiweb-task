<?php

ini_set('display_errors', '0');
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// CORS: allow Netlify frontend and preflight (must run before any output)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '/';
$basePath = rtrim($_ENV['BASE_PATH'] ?? '', '/');
if ($basePath !== '' && substr($path, 0, strlen($basePath)) === $basePath) {
    $path = substr($path, strlen($basePath)) ?: '/';
}
$path = '/' . trim($path, '/');
if ($path === '//') {
    $path = '/';
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->get('/', fn() => json_encode(['status' => 'ok', 'endpoints' => ['/health', '/graphql']]));
    $r->get('/health', function () {
        $dbCfg = \App\Config\Database::getConfigForHealth();
        $connected = \App\Config\Database::testConnection();
        return json_encode([
            'status' => 'ok',
            'php' => PHP_VERSION,
            'db' => [
                'host' => $dbCfg['host'],
                'port' => $dbCfg['port'],
                'user' => $dbCfg['user'],
                'password' => $dbCfg['password_masked'],
            ],
            'connected' => $connected,
        ]);
    });
    $r->get('/graphql-ping', function () {
        header('Content-Type: application/json');
        try {
            $schema = \App\GraphQL\SchemaBuilder::build();
            $result = \GraphQL\GraphQL::executeQuery($schema, '{ __typename }');
            return json_encode($result->toArray());
        } catch (\Throwable $e) {
            return json_encode(['errors' => [['message' => $e->getMessage(), 'code' => get_class($e)]]]);
        }
    });
    $r->post('/graphql', [App\Controller\GraphQL::class, 'handle']);
});
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $path);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $path === '/' && file_exists(__DIR__ . '/index.html') && !isset($_GET['json'])) {
            readfile(__DIR__ . '/index.html');
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not Found']);
        }
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $result = call_user_func_array($handler, [$vars]);
        if (is_string($result) && substr($result, 0, 1) === '{') {
            header('Content-Type: application/json; charset=UTF-8');
        }
        echo $result;
        break;
}
