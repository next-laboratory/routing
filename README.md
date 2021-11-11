一款简单的路由,目前需要安装max/foundation
```php
$routeCollector = new RouteCollector();

$router = new Router($routeCollector);

$router->get('index', function() { 
    return 'test'; 
});

$router->prefix('api')->middleware('api')->group(function(Router $router) {
    $router->get('/user', function() {
        var_dump('user');
    })->middleware('auth');
})

$route = $routeCollercor->resolve();

var_dump($route);
```