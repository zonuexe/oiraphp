<?php

function h(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
