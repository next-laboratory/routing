<?php

namespace Max\Routing\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class GetMapping extends RuleMapping
{
    protected array $methods = ['HEAD', 'GET'];
}