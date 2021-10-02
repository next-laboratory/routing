<?php

namespace Max\Routing;

use Max\Routing\Exceptions\RouteNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

class RouteCollector
{
    /**
     * 未分组的全部路由
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * 按照请求方式分组的路由
     *
     * @var array
     */
    protected $grouped = [];

    /**
     * 添加一个路由
     *
     * @param Route $route
     *
     * @return $this
     */
    public function add(Route $route)
    {
        $this->routes[] = $route;
        foreach ($route->getMethods() as $method) {
            $this->grouped[$method][$route->getUri()] = $route;
        }
        return $this;
    }

    /**
     * 直接替换路由
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
     * 全部
     *
     * @return array
     */
    public function all()
    {
        return $this->routes;
    }

    public function getGrouped()
    {
        return $this->grouped;
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
        $requestUri = $request->getUri()->getPath();
        foreach ($this->grouped[$request->getMethod()] as $route) {
            /* @var Route $route */
            $uri = $route->getUri();
            if ($uri === $requestUri || preg_match('#^' . $uri . '$#iU', $requestUri, $match)) {
                if (isset($match)) {
                    array_shift($match);
                    $route->routeParams($match);
                }
                return $route;
            }
        }
        throw new RouteNotFoundException('Page not found.', 404);
    }

}
