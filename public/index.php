<?php

require __DIR__ . '/../vendor/autoload.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routes = require __DIR__ . '/../app/routes.php';

$map = [];
foreach ($routes as [$method, $path, $f]) {
    if (empty($path)) {
        $map[$path]  = [];
    }

    $map[$path][$method] = $f;
}

if (isset($map[$request_uri][$request_method])) {
    $map[$request_uri][$request_method]();
} else {
    http_response_code(404);
    echo "<p>404 Not Found</p>";
}
