<?php

namespace Oira;

use Closure;
use ReflectionFunction;
use ReflectionParameter;

class ClosureReflector
{
    /** @var \Closure */
    private $closure;
    /** @var ReflectionFunction */
    private $ref;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
        $this->ref = new ReflectionFunction($this->closure);
    }

    /**
     * @params array<string,mixed> $values
     * @throws \ReflectionException
     */
    public function dispatch(array $values)
    {
        $params = $this->ref->getParameters();

        if (count($params) == 1 && $params[0]->getName() === 'params') {
            $args = array_values($values);
        } else {
            $args = self::_buildArgs($params, $values);
        }

        return ($this->closure)(...$args);
    }

    /**
     * @param ReflectionParameter[] $params
     * @params array<string,mixed> $values
     */
    public static function _buildArgs(array $params, array $values): array
    {
        $args = [];

        foreach ($params as $param) {
            $name = $param->getName();
            if (!isset($values[$name])) {
                throw new \LogicException('ルーティング定義と関数定義が一致しません');
            }

            $args[] = self::_castArg($param, $values[$name]);
        }

        return $args;
    }

    public static function _castArg(ReflectionParameter $param, $value)
    {
        if (!$param->hasType()) {
            return $value;
        }

        $type = (string)$param->getType();

        if ($type === 'int') {
            if (!(is_string($value) && ctype_digit($value))) {
                throw new \TypeError('');
            }

            return (int)$value;
        }

        if ($type === 'string') {
            if (!is_string($value)) {
                throw new \TypeError('');
            }

            return $value;
        }

        throw new \LogicException('Unexpected Value');
    }

    /**
     * @return array<string,string>
     */
    public function getParamTypes(): array
    {
        $params = [];

        foreach ($this->ref->getParameters() as $p) {
            $type = $p->hasType() ? (string)$p->getType() : 'mixed';
            $name = $p->getName();
            $params[$name] = $type;
        }

        return $params;
    }
}
