<?php
declare (strict_types=1);

namespace Max\Routing;

use Max\App;
use Max\Routing\Exceptions\RouteNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 路由操作类
 * Class Router
 *
 * @package Max\Http
 * @author  chengyao
 */
class Router
{
    /**
     * 分组中间件
     *
     * @var array
     */
    protected array $middlewares = [];

    /**
     * 前缀
     *
     * @var string
     */
    protected string $prefix = '';

    /**
     * 路由集合
     *
     * @var RouteCollector
     */
    protected RouteCollector $routeCollector;

    /**
     * @param string          $prefix
     * @param array           $middlewares
     * @param ?RouteCollector $routeCollector
     */
    public function __construct(RouteCollector $routeCollector, string $prefix = '', $middlewares = [])
    {
        $this->prefix         = $prefix;
        $this->middlewares    = (array)$middlewares;
        $this->routeCollector = $routeCollector;
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function patch(string $uri, $destination)
    {
        return $this->rule($uri, $destination, ['PATCH']);
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function put(string $uri, $destination)
    {
        return $this->rule($uri, $destination, ['PUT']);
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function delete(string $uri, $destination)
    {
        return $this->rule($uri, $destination, ['DELETE']);
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function post(string $uri, $destination)
    {
        return $this->rule($uri, $destination, ['POST']);
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function get(string $uri, $destination)
    {
        return $this->rule($uri, $destination, ['GET', 'HEAD']);
    }

    /**
     * @param string   $uri
     * @param          $destination
     * @param string[] $methods
     *
     * @return \Max\Routing\Route
     */
    public function rule(string $uri, $destination, $methods = ['GET', 'HEAD', 'POST'])
    {
        $route = new Route([
            'uri'         => '/' . trim($this->prefix . $uri, '/'),
            'destination' => $destination,
            'methods'     => $methods,
            'middleware'  => $this->middlewares
        ]);
        $this->routeCollector->add($route);
        return $route;
    }

    /**
     * 分组路由
     *
     * @param $group
     */
    public function group($group)
    {
        $route = App::getInstance()->route;
        App::getInstance()->set('route', $this);
        if ($group instanceof \Closure) {
            $group($this);
        } else if (is_file($group)) {
            include($group);
        }
        App::getInstance()->set('route', $route);
    }

    /**
     * 设置中间件
     *
     * @param $middleware
     *
     * @return $this
     */
    public function middleware($middleware)
    {
        return new static($this->routeCollector, $this->prefix, [...$this->middlewares, ...(array)$middleware]);
    }

    /**
     * 设置前缀
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function prefix(string $prefix)
    {
        return new static($this->routeCollector, $this->prefix . $prefix, $this->middlewares);
    }

}
