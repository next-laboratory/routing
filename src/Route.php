<?php

namespace Max\Routing;

class Route
{
    /**
     * @var string
     */
    protected string $uri;

    /**
     * @var array
     */
    protected array $methods;

    /**
     * @var \Closure|array|string
     */
    protected $destination;

    /**
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
     * @var false|int
     */
    protected bool $cache = false;

    /**
     * 别名
     *
     * @var string|null
     */
    protected ?string $alias = null;

    /**
     * 跨域允许
     *
     * @var null
     */
    protected ?array $allowCrossDomain = null;

    /**
     * 路由参数
     *
     * @var array
     */
    protected array $routeParams = [];

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

    public function __get($key)
    {
        return $this->$key ?? null;
    }

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

//    /**
//     * @return callable
//     */
//    public function getDestination()
//    {
//        if (is_string($this->destination)) {
//            if ('C:' === substr($this->destination, 0, 2)) {
//                return \Opis\Closure\unserialize($this->destination);
//            }
//            $destination = explode('@', $this->destination, 2);
//            if (2 !== count($destination)) {
//                throw new \InvalidArgumentException('路由参数不正确!');
//            }
//            return $destination;
//        }
//        return $this->destination;
//    }

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
        return $this->set(__FUNCTION__, $ext);
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
        return $this->set(__FUNCTION__, array_unique([...$this->middleware, ...(array)$middleware]), true);
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
        return $this->set(__FUNCTION__, $expire);
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
        return $this->set(__FUNCTION__, $allowDomain, true);
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
        return $this->set(__FUNCTION__, $alias);
    }

    protected function set(string $key, $value, bool $arrayFlag = false)
    {
        $this->$key = $arrayFlag ? (array)$value : $value;
        return $this;
    }

}