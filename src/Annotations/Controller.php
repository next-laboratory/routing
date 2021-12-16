<?php
declare(strict_types=1);

namespace Max\Routing\Annotations;

use Max\Di\Annotations\Annotation;
use Max\Routing\RouteCollector;
use Max\Routing\Router;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Controller extends Annotation
{
    protected string $prefix     = '';
    protected array  $middleware = [];

    public function __construct(...$args)
    {
        parent::__construct($args);
        RouteCollector::$router = new Router([
            'prefix'     => $this->prefix,
            'middleware' => (array)$this->middleware,
        ]);
    }

    public function __destruct()
    {
        RouteCollector::refresh();
    }

}
