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
    protected static array $routes = [];

    /**
     * @var Router
     */
    public static Router $router;

    /**
     * 添加一个路由
     *
     * @param Route $route
     *
     * @return $this
     */
    public static function add(Route $route)
    {
        foreach ($route->methods as $method) {
            static::addWithMethod($method, $route);
        }
    }

    /**
     * 添加到分组后的路由中
     *
     * @param       $method
     * @param Route $route
     */
    public static function addWithMethod($method, Route $route)
    {
        static::$routes[$method][] = $route;
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
        static::$routes = $routes;
    }

    /**
     * 全部
     *
     * @return array
     */
    public function all(): array
    {
        return static::$routes;
    }

    /**
     * 匹配
     *
     * @param ServerRequestInterface $request
     *
     * @return Route
     * @throws RouteNotFoundException
     */
    public static function resolve(ServerRequestInterface $request): Route
    {
        $requestUri    = $request->getUri()->getPath();
        $requestMethod = $request->getMethod();
        if (!isset(static::$routes[$requestMethod])) {
            throw new RouteNotFoundException('Method Not Allowed : ' . $requestMethod, 405);
        }
        foreach (static::$routes[$requestMethod] as $route) {
            /* @var Route $route */
            $uri = $route->uri;
            if ($uri === $requestUri || preg_match('#^' . $uri . '$#iU', $requestUri, $match)) {
                if (isset($match)) {
                    array_shift($match);
                    $route->routeParams = $match;
                }
                $route->destination = static::parseDestination($route->destination);

                return $route;
            }
        }
        throw new RouteNotFoundException('Not Found', 404);
    }

    /**
     * 将字符串地址解析为callable
     *
     * @param $destination
     *
     * @return false|mixed|string[]
     */
    protected function parseDestination($destination)
    {
        if (is_string($destination) && strpos($destination, '@')) {
            $destination = explode('@', $destination, 2);
        }

        return $destination;
    }
}
