<?php

namespace Max\Routing\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PostMapping extends RuleMapping
{
    protected array $methods = ['POST'];
}