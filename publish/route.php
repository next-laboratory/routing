<?php

return [
    'cache'          => true,
    'use_annotation' => true,
    'annotation'     => [
        'scan_dir' => env('root_path') . 'app/Http/Controllers',
        'base_dir' => 'app/Http',
    ],
];