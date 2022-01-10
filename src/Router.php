<?php
declare (strict_types=1);

namespace Max\Routing;

/**
 * @class   Router
 * @author  ChengYao
 * @date    2022/1/1
 * @time    23:09
 * @package Max\Routing
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
     * @var string|null
     */
    protected ?string $controller = null;

    /**
     * @var string
     */
    protected string $namespace = '';

    /**
     * @var array
     */
    protected array $where = [];

    /**
     * @var string
     */
    protected string $ext = '';

    /**
     * @param string $prefix
     * @param array  $middlewares
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        RouteCollector::$router = $this;
    }

    /**
     * @param string $uri
     * @param        $action
     *
     * @return \Max\Routing\Route
     */
    public function patch(string $uri, $action)
    {
        return $this->request($uri, $action, ['PATCH']);
    }

    /**
     * @param string $uri
     * @param        $action
     *
     * @return \Max\Routing\Route
     */
    public function put(string $uri, $action)
    {
        return $this->request($uri, $action, ['PUT']);
    }

    /**
     * @param string $uri
     * @param        $action
     *
     * @return \Max\Routing\Route
     */
    public function delete(string $uri, $action)
    {
        return $this->request($uri, $action, ['DELETE']);
    }

    /**
     * @param string $uri
     * @param        $action
     *
     * @return \Max\Routing\Route
     */
    public function post(string $uri, $action)
    {
        return $this->request($uri, $action, ['POST']);
    }

    /**
     * @param string $uri
     * @param        $action
     *
     * @return \Max\Routing\Route
     */
    public function get(string $uri, $action)
    {
        return $this->request($uri, $action, ['GET', 'HEAD']);
    }

    /**
     * @param string $uri
     * @param        $action
     *
     * @return Route
     */
    public function options(string $uri, $action)
    {
        return $this->request($uri, $action, ['OPTIONS']);
    }

    /**
     * @param string                $uri
     * @param string|\Closure|array $action
     * @param array                 $methods
     *
     * @return \Max\Routing\Route
     */
    public function request(string $uri, $action, array $methods = ['GET', 'HEAD', 'POST'])
    {
        if (is_string($action)) {
            if (!is_null($this->controller)) {
                $action = sprintf('%s@%s', $this->controller, $action);
            }
            if ('' !== $this->namespace) {
                $action = ltrim(sprintf('%s\\%s', $this->namespace, $action), '\\');
            }
        }

        $options = [
            'uri'     => '/' . trim($this->prefix . $uri, '/'),
            'action'  => $action,
            'methods' => $methods,
        ];

        if (!empty($this->middlewares)) {
            $options['middlewares'] = $this->middlewares;
        }
        if (!empty($this->ext)) {
            $options['ext'] = $this->ext;
        }
        if (!empty($this->where)) {
            $options['where'] = $this->where;
        }

        $route = new Route($options);

        RouteCollector::add($route);

        return $route;
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
                $method = 'prepare' . \ucfirst($key);
                if (\method_exists($new, $method)) {
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
     * @param array|string $pattern
     * @param string|null  $value
     *
     * @return Router
     */
    public function where($pattern, ?string $value = null)
    {
        $new        = clone $this;
        $new->where = $new->prepareWhere($pattern, $value);

        return $new;
    }

    /**
     * @param array|string $pattern
     * @param              $value
     *
     * @return array
     */
    protected function prepareWhere($pattern, $value)
    {
        if (!is_null($value)) {
            $pattern = [$pattern => $value];
        }
        return array_merge($this->where, $pattern);
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
     * @param string $ext
     *
     * @return Router
     */
    public function ext(string $ext)
    {
        $new      = clone $this;
        $new->ext = $ext;

        return $new;
    }

    /**
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

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return string|null
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
