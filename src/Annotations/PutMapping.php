<?php

namespace Max\Routing\Annotations;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PutMapping extends RuleMapping
{
    protected array $methods = ['PUT'];
}