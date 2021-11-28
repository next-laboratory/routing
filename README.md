一款简单的路由, 可以独立使用

```php

$router = new Router();

$router->get('index', function() {
    return 'test';
});

// 路由分组示例
$router->prefix('api')->middleware('api')->group(function(Router $router) {
    $router->get('/user', function() {
        var_dump('user');
    })->middleware('auth');
    $router->middleware('user')->group(function() {
        //
    }
})

// 解析路由，返回匹配到的Route对象, $request必须实现Psr ServerRequestInterface
$route = RouteCollector::resolve($request);

var_dump($route);
```

如果你使用了MaxPHP，那么可以直接使用路由的门面

```php
use Max\Foundation\Facades\Route;

Route::prefix('/v1')->middleware('ai')->group(function() {
    Route::rule('/users', function() {
        $name = \Max\Foundation\Facades\Request::get('name', 'MaxPHP!');
        return ['code' => 0, 'message' => 'Hello, ' . $name];
    })->allowCrossDomain('*');
});
```
