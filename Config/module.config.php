<?php

/**
 * Module configuration container
 */

return array(
    'name' => 'Photogallery',
    'description' => 'Photogallery module allows you to easily manage photo-galleries on your site',
    // Bookmarks of this module
    'bookmarks' => array(
        array(
            'name' => 'Add a photo',
            'controller' => 'Photogallery:Admin:Photo@addAction',
            'icon' => 'glyphicon glyphicon-picture'
        )
    ),
    'menu' => array(
        'name' => 'Photogallery',
        'icon' => 'fas fa-camera',
        'items' => array(
            array(
                'route' => 'Photogallery:Admin:Browser@indexAction',
                'name' => 'View all photos'
            ),
            array(
                'route' => 'Photogallery:Admin:Photo@addAction',
                'name' => 'Add a photo'
            ),
            array(
                'route' => 'Photogallery:Admin:Album@addAction',
                'name' => 'Add album'
            ),
            array(
                'route' => 'Photogallery:Admin:Config@indexAction',
                'name' => 'Configuration'
            )
        )
    )
);