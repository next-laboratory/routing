<?php

namespace Max\Routing\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class DeleteMapping extends RuleMapping
{
    protected $methods = ['DELETE'];
}