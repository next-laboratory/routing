<?php

namespace Max\Routing;

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function test__construct()
    {
        $middleware = [1, 2, 3];
        $prefix     = 'api';
        $controller = 'IndexController';
        $namespace  = 'App\Http';
        $router     = new Router([
            'middlewares' => $middleware,
            'prefix'      => $prefix,
            'controller'  => $controller,
            'namespace'   => $namespace,
        ]);
        $route      = $router->get('/', 'index');
        self::assertEquals($route->getMiddlewares(), $middleware);
        self::assertEquals($route->getAction(), $namespace . '\\' . $controller . '@index');
    }

    public function testMiddleware()
    {
        $router = new Router();
        $route  = $router->middleware([1, 2, 3])
                         ->request('/test', 'IndexController@index')
                         ->middleware([4, 5, 6]);

        self::assertEquals($route->getMiddlewares(), [1, 2, 3, 4, 5, 6]);
    }

    public function testPrefix()
    {
        $router = new Router();
        $route  = $router->prefix('/api')
                         ->request('/users', 'TestController@test');
        self::assertEquals($route->getUri(), '/api/users');
    }
}
