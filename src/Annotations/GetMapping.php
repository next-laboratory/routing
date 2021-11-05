<?php

namespace Max\Routing\Annotations;

#[\Attribute(\Attribute::TARGET_METHOD)]
class GetMapping extends RequestMapping
{
    protected array $methods = ['HEAD', 'GET'];
}