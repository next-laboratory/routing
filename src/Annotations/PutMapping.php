<?php

namespace Max\Routing\Annotations;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PutMapping extends RequestMapping
{
    protected array $methods = ['PUT'];
}