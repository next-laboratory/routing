<?php

return [
    // 路由缓存
    'cache'      => false,
    // 路由注解
    'annotation' => [
        'enable'   => false,
        'scan_dir' => env('root_path') . 'app/Http/Controllers',
        'base_dir' => 'app/Http/Controllers',
    ],
];
