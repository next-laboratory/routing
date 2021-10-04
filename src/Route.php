<?php

namespace Max\Routing;

class Route
{
    /**
     * 请求URI
     *
     * @var string
     */
    protected string $uri;

    /**
     * 请求方法
     *
     * @var array
     */
    protected array $methods;

    /**
     * 目标
     *
     * @var \Closure|array|string
     */
    protected $destination;

    /**
     * 中间件
     *
     * @var array
     */
    protected array $middleware = [];

    /**
     * 后缀
     *
     * @var string
     */
    protected string $ext = '';

    /**
     * 是否缓存
     *
     * @var false|int
     */
    protected $cache = false;

    /**
     * 别名
     *
     * @var ?string
     */
    protected ?string $alias = null;

    /**
     * 跨域允许
     *
     * @var ?array
     */
    protected ?array $allowCrossDomain = null;

    /**
     * 路由参数
     *
     * @var array
     */
    protected array $routeParams = [];

    /**
     * 路由集合
     *
     * @var RouteCollector
     */
    protected RouteCollector $routeCollector;

    /**
     * 初始化数据
     * Route constructor.
     *
     * @param iterable $route
     */
    public function __construct(iterable $route)
    {
        foreach ($route as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * 排除某个中间件
     *
     * @param string $middleware
     *
     * @return $this
     */
    public function withoutMiddleware(string $middleware)
    {
        if ($key = array_search($middleware, $this->middleware)) {
            unset($this->middleware[$key]);
        }
        return $this;
    }

    /**
     * 后缀
     *
     * @param string $ext
     *
     * @return $this
     */
    public function ext(string $ext)
    {
        $this->uri .= $ext;
        return $this->call(__FUNCTION__, $ext);
    }

    /**
     * 设置中间件
     *
     * @param array|string $middleware
     *
     * @return $this
     */
    public function middleware($middleware)
    {
        return $this->call(__FUNCTION__, array_unique([...$this->middleware, ...(array)$middleware]), true);
    }

    /**
     * 缓存
     *
     * @param int $expire
     *
     * @return $this
     */
    public function cache(int $expire)
    {
        return $this->call(__FUNCTION__, $expire);
    }

    /**
     * 允许跨域
     *
     * @param string|array $allowDomain
     *
     * @return $this
     */
    public function allowCrossDomain($allowDomain)
    {
        return $this->call(__FUNCTION__, $allowDomain, true);
    }

    /**
     * 别名
     *
     * @param string $alias
     *
     * @return $this
     */
    public function alias(string $alias)
    {
        $this->routeCollector->getUrl()->set($alias, $this->uri);
        return $this->call(__FUNCTION__, $alias);
    }

    protected function call(string $key, $value, bool $arrayFlag = false)
    {
        $this->$key = $arrayFlag ? (array)$value : $value;
        return $this;
    }

    public function __get($key)
    {
        return $this->$key ?? null;
    }

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

}