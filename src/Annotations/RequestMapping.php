<?php
declare(strict_types=1);

namespace Max\Routing\Annotations;

use Max\Di\Annotations\Annotation;
use Max\Routing\Contracts\MappingInterface;
use Max\Routing\RouteCollector;

/**
 * @class   RequestMapping
 * @author  ChengYao
 * @date    2021/12/26
 * @time    11:20
 * @package Max\Routing\Annotations
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class RequestMapping extends Annotation implements MappingInterface
{
    /**
     * @var string
     */
    protected string $path;

    /**
     * @var string|null
     */
    protected ?string $alias = null;

    /**
     * @var null
     */
    protected $allowCrossDomain = null;

    /**
     * @var string
     */
    protected string $ext = '';

    /**
     * @var array
     */
    protected array $where = [];

    /**
     * @var array|string[]
     */
    protected array $methods = ['GET', 'HEAD', 'POST'];

    /**
     * @param string $controller
     * @param string $method
     *
     * @return void
     */
    public function register(string $controller, string $method)
    {
        $route = RouteCollector::$router->request($this->path, $controller . '@' . $method, $this->methods);
        if ($this->allowCrossDomain) {
            $route->allowCrossDomain((array)$this->allowCrossDomain);
        }
        if ($this->alias) {
            $route->alias($this->alias);
        }
        if ($this->ext) {
            $route->ext($this->ext);
        }
        if ($this->where) {
            $route->where($this->where);
        }
    }
}
