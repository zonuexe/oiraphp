<?php

$routes = [];

$routes['/'] = function () {
    ob_start();
    include __DIR__ . '/../app/view/index.phtml';

    return [200, ['Content-Type' => 'text/html'], ob_get_clean()];
};

$routes['/phpinfo.php'] = function () {
    ob_start();
    phpinfo();

    return [200, ['Content-Type' => 'text/html'], ob_get_clean()];
};

return $routes;
