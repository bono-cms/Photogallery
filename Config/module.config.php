<?php

return array(
    'name' => 'Photogallery',
    'caption' => 'Photogallery',
    'route' => 'Photogallery:Admin:Browser@indexAction',
    'order' => 1,
    'description' => 'Photogallery module allows you to easily manage photo-galleries on your site',

    // Bookmarks of this module
    'bookmarks' => array(
        array(
            'name' => 'Add a photo',
            'controller' => 'Photogallery:Admin:Photo@addAction',
            'icon' => 'glyphicon glyphicon-picture'
        )
    )
);