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
    'fizzbuzz' => ['GET', '/fizzbuzz/{number}{ext}', function (int $number, string $ext) {
        $fizzbuzz = array_map(function ($n) {
            return ['01' => 'Fizz', '10' => 'Buzz', '11' => 'FizzBuzz'][(int)($n%3==0).(int)($n%5==0)] ?? (string)$n;
        }, range(1, $number));

        if ($ext === '.json') {
            return [200, ['Content-Type' => 'application/json'], json_encode($fizzbuzz)];
        } else {
            return [200, ['Content-Type' => 'text/plain'], implode("\n", $fizzbuzz)];
        }
    }, ['ext' => '(?:\\.(?:json|txt))?']],
    '#404' => function () {
        return [404, [], '<p>404 Not Found</p>'];
    },
    '#pattern' => [],
];

return $routes;
