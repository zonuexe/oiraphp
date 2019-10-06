<?php

require __DIR__ . '/../vendor/autoload.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new \Oira\Routing(require __DIR__ . '/../app/routes.php');
[$status, $headers, $body] = $router[[$request_method, $request_uri]]();

http_response_code($status);
foreach ($headers as $h) {
    header($h);
}

if ($body !== null) {
    echo $body;
}
