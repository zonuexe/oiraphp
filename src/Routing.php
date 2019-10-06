<?php

namespace Oira;


class Routing implements \ArrayAccess
{
    /** @var array<string,array<string,callable>> URLをキーにしたルーティング対応表 */
    private $static_map;
    /** @var array<string,array<string,callable>> URLをキーにしたルーティング対応表 */
    private $dynamic_map;
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
        $static_map = [];
        $dynamic_map = [];
        $names = [];

        $_404 = $routes['#404'] ?? function () {};
        unset($routes['#404']);
        $this->special['#404'] = $_404;

        $regexp_pattenrs = $routes['#pattern'] ?? [];
        unset($routes['#pattern']);

        foreach ($routes as $name => $r) {
            [$method, $path, $f] = $r;
            $patterns = $r[3] ?? [];

            $names[$name] = [$path];

            if (strpos($path, '{') === false) {
                if (empty($path)) {
                    $static_map[$path] = [];
                }

                $static_map[$path][$method] = $f;
            } else {
                $pattern = $this->buildPattern($path, $f, $patterns + $regexp_pattenrs);
                $dynamic_map[$pattern][$method] = $f;
            }
        }

        $this->static_map = $static_map;
        $this->dynamic_map = $dynamic_map;
        $this->names = $names;
    }

    /**
     * マッチした関数を返す
     */
    public function match(string $request_method, string $request_uri): \Closure
    {
        if ($this->static_map[$request_uri][$request_method]) {
            return $this->static_map[$request_uri][$request_method];
        }

        return $this->special['#404'];
    }

    /**
     * マッチした関数を返す
     *
     * @return array{0:\Closure,1:array}
     */
    public function matched(string $request_method, string $request_uri): array
    {
        $matches = null;
        $closure = null;

        if (isset($this->static_map[$request_uri][$request_method])) {
            $closure = $this->static_map[$request_uri][$request_method];
        } else {
            foreach ($this->dynamic_map as $pattern => $method_closure) {
                if (!isset($method_closure[$request_method])) {
                    continue;
                }
                if (preg_match($pattern, $request_uri, $matches)) {
                    $closure = $method_closure[$request_method];
                    break;
                }
            }
        }

        return [$closure ?? $this->special['#404'], $matches ?? []];
    }

    public function offsetExists($offset)
    {
        if (!is_array($offset) || count($offset) !== 2) {
            return false;
        }

        [$method, $uri] = $offset;

        return isset($this->static_map[$uri][$method]);
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

    public static function buildPattern(string $path, \Closure $closure, array $regexp_patterns)
    {
        $ref = new ClosureReflector($closure);
        $param_types = $ref->getParamTypes();

        if (!preg_match_all('/\{([_a-z0-9]+)\}/', $path, $m)) {
            throw new \LogicException('This pattern has not any parameter');
        }

        array_shift($m);
        $param_names = array_column($m, 0);

        $new_pattern = '@\A' . preg_replace_callback('/\{([_a-z0-9]+)\}/', function ($matches) use ($param_types, $regexp_patterns) {
            $name = $matches[1];
            $type = $param_types[$name] ?? null;

            if ($type === null) {
                throw new \LogicException('Unexpected parameter.');
            }

            if (isset($regexp_patterns[$name])) {
                $type_pattern = $regexp_patterns[$name];
            } elseif ($type === 'int') {
                $type_pattern = '-?[1-9]*[0-9]+';
            } elseif ($type === 'string' || $type === 'mixed') {
                $type_pattern = '[^/]+';
            } else {
                throw new \LogicException('Unexpected type.');
            }

            return "(?<{$name}>{$type_pattern})";
        }, $path) . '\z@u';

        return $new_pattern;
    }
}
