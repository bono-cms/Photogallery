<?php

/**
 * Module configuration container
 */

return [
    'name' => 'Photogallery',
    'description' => 'Photogallery module allows you to easily manage photo-galleries on your site',
    'bookmarks' => [
        [
            'name' => 'Add a photo',
            'controller' => 'Photogallery:Admin:Photo@addAction',
            'icon' => 'fas fa-camera'
        ]
    ],
    'menu' => [
        'name' => 'Photogallery',
        'icon' => 'fas fa-camera',
        'items' => [
            [
                'route' => 'Photogallery:Admin:Browser@indexAction',
                'name' => 'View all photos'
            ],
            [
                'route' => 'Photogallery:Admin:Photo@addAction',
                'name' => 'Add a photo'
            ],
            [
                'route' => 'Photogallery:Admin:Album@addAction',
                'name' => 'Add album'
            ],
            [
                'route' => 'Photogallery:Admin:Config@indexAction',
                'name' => 'Configuration'
            ]
        ]
    ]
];