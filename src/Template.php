<?php
namespace Oira;
class Template
{
    /** @var string ファイルパス */
    private $file;
    /** @var array テンプレートで表示するための変数を保持した配列 */
    private $params;

    public function __construct($file, array $params)
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
