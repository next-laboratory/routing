<?php

namespace Max\Routing\Contracts;

interface MappingInterface
{
    /**
     * @param string $controller 请求的控制器
     * @param string $method     请求的方法
     *
     * @return mixed
     */
    public function set(string $controller, string $method);

    public function register();
}