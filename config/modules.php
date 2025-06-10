<?php
// config/modules.php

return [
    'modules' => [
        'User' => [
            'path' => app_path('Modules/User'),
            'namespace' => 'App\\Modules\\User',
        ],
        'Post' => [
            'path' => app_path('Modules/Post'),
            'namespace' => 'App\\Modules\\Post',
        ],
        'Category' => [
            'path' => app_path('Modules/Category'),
            'namespace' => 'App\\Modules\\Category',
        ],
        'Comment' => [
            'path' => app_path('Modules/Comment'),
            'namespace' => 'App\\Modules\\Comment',
        ],
        'Media' => [
            'path' => app_path('Modules/Media'),
            'namespace' => 'App\\Modules\\Media',
        ],
        'Dashboard' => [
            'path' => app_path('Modules/Dashboard'),
            'namespace' => 'App\\Modules\\Dashboard',
        ],
    ]
];