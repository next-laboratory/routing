<?php

namespace Max\Routing\Tests;

use Max\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{

    protected Route $route;

    protected function setUp(): void
    {
        $this->route = new Route();
        parent::setUp();
    }

    public function testCache()
    {
        $this->route->cache(100);
        self::assertEquals(100, $this->route->cache);
    }

    public function test__construct()
    {
        $this->route = new Route(['cache' => 100, 'middleware' => ['TestMiddleware']]);
        self::assertEquals(100, $this->route->cache);
        self::assertEquals(['TestMiddleware'], $this->route->middleware);
    }
}
