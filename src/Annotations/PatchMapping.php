<?php

namespace Max\Routing\Annotations;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PatchMapping extends RuleMapping
{
    protected array $methods = ['PATCH'];
}