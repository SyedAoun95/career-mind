<?php
// Keep PHP notices/deprecations out of the page output; log them instead.
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../routes/web.php';

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../app/';
    $classPath = str_replace('App\\', '', $class);
    $file = $baseDir . str_replace('\\', '/', $classPath) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

$routes = require __DIR__ . '/../routes/web.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

$matched = false;
foreach ($routes as $route) {
    if ($route['method'] === $method && $route['path'] === $path) {
        $matched = true;
        $controller = $route['controller'];
        $action = $route['action'];
        $instance = new $controller();
        $instance->$action();
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    echo '404 - Page not found';
}
