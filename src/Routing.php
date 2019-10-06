<?php

namespace Oira;


class Routing implements \ArrayAccess
{
    /** @var array<string,array<string,callable>> URLをキーにしたルーティング対応表 */
    private $map;
    /** @var array<string,array{0:string,1?:array<string,string>}> 名前をキーにしたURLとパラメータの対応表 */
    private $names;
    /** @var array<string,\Closure> */
    private $special = [];

    /**
     * Routing constructor.
     * @param array<string,array{0:string,1:string,2:callable}> $routes
     */
    public function __construct(array $routes)
    {
        $map = [];
        $names = [];

        $_404 = $routes['#404'] ?? function () {};
        unset($routes['#404']);
        $this->special['#404'] = $_404;

        foreach ($routes as $name => [$method, $path, $f]) {
            if (empty($path)) {
                $map[$path]  = [];
            }

            $names[$name] = [$path];
            $map[$path][$method] = $f;
        }

        $this->map = $map;
        $this->names = $names;
    }

    /**
     * マッチした関数を返す
     */
    public function match(string $request_method, string $request_uri): \Closure
    {
        if ($this->map[$request_uri][$request_method]) {
            return $this->map[$request_uri][$request_method];
        }

        return $this->special['#404'];
    }

    public function offsetExists($offset)
    {
        if (!is_array($offset) || count($offset) !== 2) {
            return false;
        }

        [$method, $uri] = $offset;

        return isset($this->map[$uri][$method]);
    }

    /**
     * @param array $offset
     * @return bool|\Closure|mixed
     */
    public function offsetGet($offset)
    {
        if (!is_array($offset) || count($offset) !== 2) {
            throw new \OutOfRangeException('Offset must array{0:string,1:string}');
        }

        [$method, $uri] = $offset;

        return $this->match($method, $uri);
    }

    public function offsetSet($offset, $value)
    {
        throw new \OutOfRangeException('Must not be set in this class');
    }

    public function offsetUnset($offset)
    {
        throw new \OutOfRangeException('Must not be set in this class');
    }
}
