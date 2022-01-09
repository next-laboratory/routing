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
     * 编译后的Uri
     *
     * @var string|null
     */
    public ?string $compiledUri = null;

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
     * 路由参数规则
     *
     * @var array
     */
    protected array $where = [];

    /**
     * 路由参数
     *
     * @var array
     */
    protected array $parameters = [];

    /**
     * 初始化数据
     * Route constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            if (\property_exists($this, $key)) {
                if ('ext' === $key) {
                    $this->ext($value);
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * 设置路由参数规则
     *
     * @param        $key
     * @param string $rule
     *
     * @return $this
     */
    public function where($key, string $rule)
    {
        $this->where[$key] = $rule;

        return $this;
    }

    /**
     * 获取路由参数规则
     *
     * @param string $key
     *
     * @return mixed|string
     */
    public function getWhere(string $key)
    {
        return $this->where[$key] ?? '[^\/]+';
    }

    /**
     * 设置单个路由参数
     *
     * @param string $name
     * @param        $value
     *
     * @return void
     */
    public function setParameter(string $name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * 设置路由参数，全部
     *
     * @param array $parameters
     *
     * @return void
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * 获取单个路由参数
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getParameter(string $name): ?string
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * 获取全部路由参数
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
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
        $this->uri .= '.' . $ext;
        $this->ext = $ext;

        return $this;
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
        $this->middlewares = \array_unique([...$this->middlewares, ...(array)$middleware]);

        return $this;
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
        $this->cache = $expire;

        return $this;
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
        $this->methods[]        = 'OPTIONS';
        $this->allowCrossDomain = (array)$allowDomain;
        RouteCollector::addWithMethod('OPTIONS', $this);

        return $this;
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
        $this->alias = $alias;

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
     * 获取编译后的Uri
     *
     * @return array|string|string[]|null
     */
    public function getCompiledUri()
    {
        if (isset($this->compiledUri)) {
            return $this->compiledUri;
        }

        $uri = $this->getUri();
        \preg_match_all('/\{([^\/]+)\}/', $uri, $matched, PREG_PATTERN_ORDER);
        if (empty($matched)) {
            return $uri;
        }

        return $this->compiledUri = str_replace($matched[0], \array_map(function($value) {
            $where    = $this->getWhere($value);
            $nullable = '?' === $value[strlen($value) - 1];
            $value    = $nullable ? rtrim($value, '?') : $value;
            $this->setParameter($value, null);

            return sprintf('(?P<%s>%s)%s', $value, $where, $nullable ? '?' : '');
        }, $matched[1]), $uri);
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
}
