<?php

declare(strict_types=1);

namespace Max\Routing;

use Max\Routing\Exceptions\RouteNotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @class   RouteCollector
 * @author  ChengYao
 * @date    2022/1/1
 * @time    23:11
 * @package Max\Routing
 */
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
        foreach ($route->getMethods() as $method) {
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
     * @param string $method
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getByMethod(string $method)
    {
        if (isset(static::$routes[$method])) {
            return static::$routes[$method];
        }
        throw new \Exception('Method not allowed: ' . $method, 405);
    }

    /**
     * 直接替换路由
     *
     * @param array $routes
     *
     * @return $this
     */
    public static function replace(array $routes)
    {
        static::$routes = $routes;
    }

    /**
     * 全部
     *
     * @return array
     */
    public static function all(): array
    {
        return static::$routes;
    }

    /**
     * @return void
     */
    public function flush()
    {
        static::$routes = [];
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
        $ext           = '';
        if (false !== strrpos($requestUri, '.')) {
            $value = explode('.', $requestUri);
            $ext   = $value[count($value) - 1];
        }
        /* @var Route $route */
        foreach (static::getByMethod($requestMethod) as $route) {
            if ($ext === $route->getExt() && ($route->getUri() === $requestUri || preg_match('#^' . $route->getCompiledUri() . '$#iU', $requestUri, $match))) {
                if (!empty($match)) {
                    foreach ($route->getParameters() as $key => $value) {
                        if (array_key_exists($key, $match)) {
                            $route->setParameter($key, $match[$key]);
                        }
                    }
                }
                return $route;
            }
        }
        throw new RouteNotFoundException('Not Found', 404);
    }

    /**
     * 导出
     *
     * @return array
     */
    public static function export()
    {
        $export = [];
        foreach (static::$routes as $method => $routes) {
            /* @var Route $route */
            foreach ($routes as $route) {
                $export[$method][] = [
                    'uri'              => $route->getUri(),
                    'methods'          => $route->getMethods(),
                    'action'           => $route->getAction(),
                    'middleware'       => $route->getMiddlewares(),
                    'ext'              => $route->getExt(),
                    'cache'            => $route->getCache(),
                    'alias'            => $route->getAlias(),
                    'allowCrossDomain' => $route->getAllowCrossDomain(),
                    'routeParams'      => $route->getParameters()
                ];
            }
        }
        return $export;
    }

    /**
     * 导入
     *
     * @param array $import
     */
    public static function import(array $import)
    {
        foreach ($import as $methods => $routes) {
            foreach ($routes as $route) {
                static::add(new Route($routes));
            }
        }
    }

    /**
     * 刷新
     *
     * @return void
     */
    public static function refresh()
    {
        static::$router = new Router();
    }
}
