<?php

namespace Max\Routing\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PatchMapping extends RuleMapping
{
    protected array $methods = ['PATCH'];
}