<?php

namespace Max\Routing\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PutMapping extends RuleMapping
{
    protected array $methods = ['PUT'];
}