<?php
declare (strict_types=1);

namespace Max\Routing;

use Max\Routing\Exceptions\RouteNotFoundException;
use Max\Routing\{Route, RouteCollector};
use Psr\Http\Message\ServerRequestInterface;
use Max\App;

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
     * @param string $prefix
     * @param array  $middlewares
     * @param null   $routeCollector
     */
    public function __construct(string $prefix = '', $middlewares = [], $routeCollector = null)
    {
        $this->prefix         = $prefix;
        $this->middlewares    = (array)$middlewares;
        $this->routeCollector = $routeCollector ?? new RouteCollector();
    }

    /**
     * 设置所有路由
     *
     * @param array $routes
     *
     * @return $this
     */
    public function make(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * 取出所有路由
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->routeCollector->all();
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
     * 设置中间件[通常是分组]
     *
     * @param $middleware
     *
     * @return $this
     */
    public function middleware($middleware)
    {
        return new static($this->prefix, [...$this->middlewares, ...(array)$middleware], $this->routeCollector);
    }

    /**
     * 设置前缀[通常是分组]
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function prefix(string $prefix)
    {
        return new static($this->prefix . $prefix, $this->middlewares, $this->routeCollector);
    }

    /**
     * 匹配
     *
     * @param ServerRequestInterface $request
     *
     * @return Route
     * @throws RouteNotFoundException
     */
    public function resolve(ServerRequestInterface $request)
    {
        return $this->routeCollector->resolve($request);
    }

}
