<?php

require __DIR__ . '/../vendor/autoload.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new \Oira\Routing(require __DIR__ . '/../app/routes.php');
$router[[$request_method, $request_uri]]();
