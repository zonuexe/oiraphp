<?php

require __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_URI'] === '/') {
    echo "<!DOCTYPE html>\n";
    echo "<title>test</title>\n";
    echo "<p>現在は" . h(date('Y年m月d日H時i分s秒')). "です</p>\n";
    echo "<ul><li><a href='/phpinfo.php'><code>phpinfo()</code></a></ul>\n";
    echo "<hr>\n";
    echo "<p><a href='https://github.com/zonuexe/oiraphp'>This code is licensed under AGPL.</a></p>";
    exit;
}

if ($_SERVER['REQUEST_URI'] === '/phpinfo.php') {
    phpinfo();
    exit;
}

http_response_code(404);
echo "<p>404 Not Found</p>";
