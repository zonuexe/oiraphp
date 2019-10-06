<?php

namespace Oira;


class Template
{
    public function __construct(string $file, array $params)
    {
        $this->file = $file;
        $this->params = $params;
    }

    public function render()
    {
        extract($this->params);
        include $this->file;
    }

    public function __toString()
    {
        ob_start();
        $this->render();

        return ob_get_clean();
    }
}
