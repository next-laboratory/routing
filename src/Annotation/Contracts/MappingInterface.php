<?php

namespace Max\Routing\Annotation\Contracts;

interface MappingInterface
{
    public function set($controller, $method);

    public function register();
}