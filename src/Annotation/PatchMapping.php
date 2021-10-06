<?php

namespace Max\Routing\Annotation;

use Max\Di\Annotation\Annotation;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PatchMapping extends Annotation
{
    protected array $methods = ['PATCH'];
}