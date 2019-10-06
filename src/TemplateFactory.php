<?php

namespace Oira;

class TemplateFactory
{
    /** @var string ディレクトリ */
    private $base_directory;

    public function __construct(string $base_directory)
    {
        $base_directory = rtrim($base_directory, '/\\', );
        $this->base_directory = $base_directory;
    }

    public function create(string $name, array $params = []): Template
    {
        $file = "{$this->base_directory}/{$name}.phtml";

        return new Template($file, $params);
    }
}
