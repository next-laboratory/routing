<?php
declare(strict_types=1);

namespace Max\Routing\Annotations;

#[\Attribute(\Attribute::TARGET_METHOD)]
class PostMapping extends RequestMapping
{
    protected array $methods = ['POST'];
}
