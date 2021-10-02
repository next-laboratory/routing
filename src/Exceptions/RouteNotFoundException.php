<?php
declare(strict_types=1);

namespace Max\Routing\Exceptions;

class RouteNotFoundException
{
    public function __construct()
    {
        parent::__construct('Page not found', 404);
    }
}