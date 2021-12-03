<?php
declare(strict_types=1);

namespace Max\Routing\Contracts;

interface MappingInterface
{
    public function register(string $controller, string $method);
}
