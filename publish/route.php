<?php

return [
    // 路由缓存
    'cache'             => false,
    // 开启注解[PHP8.0有效]
    'enable_annotation' => false,
    // 注解配置
    'annotation'        => [
        'scan_dir' => env('root_path') . 'app/Http/Controllers',
        'base_dir' => 'app/Http',
    ],
];