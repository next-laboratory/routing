<?php
declare(strict_types=1);

namespace Max\Routing;

/**
 * @class   Route
 * @author  ChengYao
 * @date    2022/1/1
 * @time    23:07
 * @package Max\Routing
 */
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
    protected $action;

    /**
     * 中间件
     *
     * @var array
     */
    protected array $middlewares = [];

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
     * @var array
     */
    protected array $allowCrossDomain = [];

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
     * @param array $route
     */
    public function __construct(array $route = [])
    {
        foreach ($route as $key => $value) {
            if (\property_exists($this, $key)) {
                $this->{$key} = $value;
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
        if ($key = array_search($middleware, $this->middlewares)) {
            unset($this->middlewares[$key]);
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
        return $this->call(__FUNCTION__, \array_unique([...$this->middlewares, ...(array)$middleware]), true);
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
        $this->methods[] = 'OPTIONS';
        RouteCollector::addWithMethod('OPTIONS', $this);
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
        Url::set($alias, $this->uri);
        return $this->call(__FUNCTION__, $alias);
    }

    /**
     * @param string $key
     * @param        $value
     * @param bool   $arrayFlag
     *
     * @return $this
     */
    protected function call(string $key, $value, bool $arrayFlag = false)
    {
        $this->{$key} = $arrayFlag ? (array)$value : $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return array|\Closure|string
     */
    public function getAction()
    {
        return $this->action;
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
    public function getExt(): string
    {
        return $this->ext;
    }

    /**
     * @return false|int
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @return array
     */
    public function getAllowCrossDomain(): array
    {
        return $this->allowCrossDomain;
    }

    /**
     * @return array
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @param array|\Closure|string $action
     */
    public function setAction($action): void
    {
        $this->action = $action;
    }

    /**
     * @param array $middlewares
     */
    public function setMiddlewares(array $middlewares): void
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @param string $ext
     */
    public function setExt(string $ext): void
    {
        $this->ext = $ext;
    }

    /**
     * @param false|int $cache
     */
    public function setCache($cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @param string|null $alias
     */
    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * @param array $allowCrossDomain
     */
    public function setAllowCrossDomain(array $allowCrossDomain): void
    {
        $this->allowCrossDomain = $allowCrossDomain;
    }

    /**
     * @param array $routeParams
     */
    public function setRouteParams(array $routeParams): void
    {
        $this->routeParams = $routeParams;
    }

    /**
     * @param $key
     *
     * @return null
     * @deprecated
     */
    public function __get($key)
    {
        return $this->{$key} ?? null;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return void
     * @deprecated
     */
    public function __set($key, $value)
    {
        $this->{$key} = $value;
    }
}
