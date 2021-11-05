<?php

namespace Max\Routing\Annotations;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PostMapping extends RequestMapping
{
    protected array $methods = ['POST'];
}