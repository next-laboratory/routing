<?php

return [
    'cache'          => false,
    'use_annotation' => false,
    'annotation'     => [
        'scan_dir' => env('root_path') . 'app/Http/Controllers',
        'base_dir' => 'app/Http',
    ],
];