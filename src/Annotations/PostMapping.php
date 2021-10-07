<?php

namespace Max\Routing\Annotations;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PostMapping extends RuleMapping
{
    protected array $methods = ['POST'];
}