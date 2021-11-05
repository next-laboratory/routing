<?php

namespace Max\Routing\Annotations;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PatchMapping extends RequestMapping
{
    protected array $methods = ['PATCH'];
}