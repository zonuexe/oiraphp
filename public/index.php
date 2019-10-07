<?php

require __DIR__ . '/../vendor/autoload.php';

$routes = require __DIR__ . '/../app/routes.php';

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (isset($routes[$request_uri])) {
    $f = $routes[$request_uri];
    $f();
} else {
    http_response_code(404);
    echo "<h1>404 Not Found</h1>";
}
