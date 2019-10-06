<?php

$template = new Oira\TemplateFactory(__DIR__ . '/view/');

$routes = [
    'index' => ['GET', '/', function () use ($template) {
        return [200, [], $template->create('index')];
    }],
    'phpinfo' => ['GET', '/phpinfo.php', function () {
        ob_start();
        phpinfo();

        return [200, [], ob_get_clean()];
    }],
    '#404' => function () {
        return [404, [], '<p>404 Not Found</p>'];
    },
];

return $routes;
