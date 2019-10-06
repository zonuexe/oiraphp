<?php

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // このエラーコードが error_reporting に含まれていない場合
        return;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new \Oira\Routing(require __DIR__ . '/../app/routes.php');
[$closure, $param] = $router->matched($request_method, $request_uri);
$ref = new \Oira\ClosureReflector($closure);
[$status, $headers, $body] = $ref->dispatch($param);

http_response_code($status ?? 200);
foreach ($headers ?? [] as $h) {
    header($h);
}

if ($body !== null) {
    echo $body;
}
