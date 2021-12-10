<?php
declare (strict_types=1);

namespace Max\Routing;

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
     * @var string|null
     */
    protected ?string $controller = null;

    /**
     * @var string
     */
    protected string $namespace = '';

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
            'destination' => $this->createDestination($destination),
            'methods'     => $methods,
            'middleware'  => $this->middlewares,
        ]);
        RouteCollector::add($route);

        return $route;
    }

    /**
     * 这个并没有重用，但是还是分离出来了
     *
     * @param $destination
     *
     * @return mixed|string
     */
    protected function createDestination($destination)
    {
        if (is_string($destination)) {
            if (!is_null($this->controller)) {
                $destination = sprintf('%s@%s', $this->controller, $destination);
            }
            if ('' !== $this->namespace) {
                $destination = ltrim(sprintf('%s\\%s', $this->namespace, $destination), '\\');
            }
        }

        return $destination;
    }

    /**
     * 分组路由
     *
     * @param \Closure $group
     */
    public function group(\Closure $group, array $options = [])
    {
        $router = RouteCollector::$router;
        $new    = $this;
        if (!empty($options)) {
            $new = clone $this;
            foreach ($options as $key => $value) {
                $method = 'prepare' . ucfirst($key);
                if (method_exists($new, $method)) {
                    $new->{$key} = $new->{$method}($value);
                }
            }
        }
        RouteCollector::$router = $new;
        $group($new);
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
        $new->middlewares = $new->prepareMiddleware($middleware);

        return $new;
    }

    /**
     * @param $middleware
     *
     * @return mixed
     */
    protected function prepareMiddleware($middleware)
    {
        return array_unique([...$this->middlewares, ...(array)$middleware]);
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
        $new->prefix = $new->preparePrefix($prefix);

        return $new;
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    protected function preparePrefix(string $prefix)
    {
        return $this->prefix . $prefix;
    }

    /**
     * 如果有控制器，则namespace失效
     *
     * @param string $controller
     *
     * @return Router
     */
    public function controller(string $controller)
    {
        $new             = clone $this;
        $new->controller = $new->prepareController($controller);

        return $new;
    }

    /**
     * @param string $controller
     *
     * @return string
     */
    protected function prepareController(string $controller)
    {
        return $controller;
    }

    /**
     * 如果有控制器，则namespace失效
     *
     * @param string $namespace
     *
     * @return Router
     */
    public function namespace(string $namespace)
    {
        $new            = clone $this;
        $new->namespace = $new->prepareNamespace($namespace);

        return $new;
    }

    /**
     * @param string $namespace
     *
     * @return string
     */
    protected function prepareNamespace(string $namespace)
    {
        return sprintf('%s\\%s', $this->namespace, $namespace);
    }

}
