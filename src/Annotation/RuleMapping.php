<?php

namespace Max\Routing\Annotation;

use Max\Di\Annotation;
use Max\Facade\Route;
use Max\Routing\Annotation\Contracts\MappingInterface;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RuleMapping extends Annotation implements MappingInterface
{
    protected $path;

    protected $controller;

    protected $method;

    protected ?string $alias = null;

    protected ?array $allowCrossDomain = null;

    protected $methods = ['GET', 'HEAD', 'POST'];

    public function set($controller, $method)
    {
        $this->controller = $controller;
        $this->method     = $method;
    }

    public function register()
    {
        $route = Route::rule($this->path, $this->controller . '@' . $this->method, $this->methods)
            ->allowCrossDomain($this->allowCrossDomain);
        if ($this->alias) {
            $route->alias($this->alias);
        }
    }

}