<?php
declare(strict_types=1);

namespace Max\Routing\Annotations;

use Max\Di\Annotations\Annotation;
use Max\Routing\RouteCollector;
use Max\Routing\Router;

/**
 * @class   Controller
 * @author  ChengYao
 * @date    2021/12/26
 * @time    13:45
 * @package Max\Routing\Annotations
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Controller extends Annotation
{
    /**
     * @var string
     */
    protected string $prefix = '';
    
    /**
     * @var array
     */
    protected array $middleware = [];

    /**
     * @param ...$args
     */
    public function __construct(...$args)
    {
        parent::__construct($args);
        RouteCollector::$router = new Router([
            'prefix'      => $this->prefix,
            'middlewares' => (array)$this->middleware,
        ]);
    }

    /**
     * 刷新Router
     */
    public function __destruct()
    {
        RouteCollector::refresh();
    }
}
