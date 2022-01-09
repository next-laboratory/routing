一款简单的路由, 可以独立使用

```php

$router = new Router();

$router->get('index/{name?}', function($name = null) {
    return $name;
});

// 路由分组示例
$router->prefix('api')->middleware('api')->group(function(Router $router) {
    $router->get('/user/{id}', function($id) {
        var_dump('user');
    })->middleware('auth')->where('id', '\d+');
    $router->middleware('user')->group(function() {
        //
    }
})

// 解析路由，返回匹配到的Route对象, $request必须实现Psr ServerRequestInterface
$route = RouteCollector::resolve($request);

var_dump($route);
```

此外还有一系列方法，例如

```php
Route::namespace('App\Http\Controllers')->controller('IndexController')->get('/', 'index');
```

对于分组，`group` 方法还可以传入第二个参数`options`, 参数需要关联数组，数组的键可以是以下之一或多个

- prefix
- middleware
- controller
- namespace

使用数组传参比链式调用效率高

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
