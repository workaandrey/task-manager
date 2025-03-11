<?php
declare(strict_types=1);

use App\Core\Config;

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

Config::load();

$debug = Config::get('APP_DEBUG') === 'true';
error_reporting($debug ? E_ALL : E_ERROR | E_PARSE);

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    $container = new App\Core\Container();
    $container->registerServices();
    
    $router = new App\Core\Router();
    
    $routeDefinitions = require_once __DIR__ . '/../src/routes.php';
    $routeDefinitions($router, $container);
    
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    
} catch (Throwable $e) {
    http_response_code(500);
    
    if (Config::get('APP_ENV') === 'production') {
        echo "<h1>Server Error</h1>";
        echo "<p>An unexpected error occurred. Please try again later.</p>";
        
        error_log($e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    } else {
        echo "<h1>Application Error</h1>";
        echo "<p>Message: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>File: " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
        echo "<h2>Stack Trace:</h2>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}