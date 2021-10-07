<?php

namespace Max\Routing\Annotations;

#[\Attribute(\Attribute::TARGET_METHOD)]
class DeleteMapping extends RuleMapping
{
    protected array $methods = ['DELETE'];
}