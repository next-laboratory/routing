<?php

namespace Max\Routing\Annotations;

use Max\Di\Annotations\Annotation;
use Max\Foundation\Facades\Route;
use Max\Routing\Contracts\MappingInterface;
use Max\Routing\RouteCollector;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RequestMapping extends Annotation implements MappingInterface
{
    protected string $path;

    protected string $controller;

    protected string $method;

    protected ?string $alias = null;

    protected $allowCrossDomain = null;

    protected array $methods = ['GET', 'HEAD', 'POST'];

    public function set(string $controller, string $method)
    {
        $this->controller = $controller;
        $this->method     = $method;
    }

    public function register()
    {
        $route = RouteCollector::$router->request($this->path, $this->controller . '@' . $this->method, $this->methods);
        if ($this->allowCrossDomain) {
            $route->allowCrossDomain((array)$this->allowCrossDomain);
        }
        if ($this->alias) {
            $route->alias($this->alias);
        }
    }

}
