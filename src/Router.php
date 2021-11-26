<?php
declare (strict_types=1);

namespace Max\Routing;

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
     * @param string $prefix
     * @param array  $middlewares
     */
    public function __construct(string $prefix = '', $middlewares = [])
    {
        $this->prefix           = $prefix;
        $this->middlewares      = (array)$middlewares;
        RouteCollector::$router = $this;
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function patch(string $uri, $destination)
    {
        return $this->request($uri, $destination, ['PATCH']);
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function put(string $uri, $destination)
    {
        return $this->request($uri, $destination, ['PUT']);
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function delete(string $uri, $destination)
    {
        return $this->request($uri, $destination, ['DELETE']);
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function post(string $uri, $destination)
    {
        return $this->request($uri, $destination, ['POST']);
    }

    /**
     * @param string $uri
     * @param        $destination
     *
     * @return \Max\Routing\Route
     */
    public function get(string $uri, $destination)
    {
        return $this->request($uri, $destination, ['GET', 'HEAD']);
    }

    /**
     * @param string   $uri
     * @param          $destination
     * @param array    $methods
     *
     * @return \Max\Routing\Route
     */
    public function request(string $uri, $destination, array $methods = ['GET', 'HEAD', 'POST'])
    {
        $route = new Route([
            'uri'         => '/' . trim($this->prefix . $uri, '/'),
            'destination' => $destination,
            'methods'     => $methods,
            'middleware'  => $this->middlewares,
        ]);
        RouteCollector::add($route);

        return $route;
    }

    /**
     * 分组路由
     *
     * @param $group
     */
    public function group($group)
    {
        $router                 = RouteCollector::$router;
        RouteCollector::$router = $this;
        if ($group instanceof \Closure) {
            $group($this);
        } else if (is_file($group)) {
            include($group);
        }
        RouteCollector::$router = $router;
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
        $new              = clone $this;
        $new->middlewares = array_unique([...$this->middlewares, ...(array)$middleware]);

        return $new;
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
        $new         = clone $this;
        $new->prefix = $this->prefix . $prefix;

        return $new;
    }

}
