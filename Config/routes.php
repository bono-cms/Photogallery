<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return [
    '/module/photogallery/(:var)' => [
        'controller' => 'Album@showAction',
    ],
    
    '/%s/module/photogallery/album/add' => [
        'controller' => 'Admin:Album@addAction'
    ],

    '/%s/module/photogallery/album/edit/(:var)' => [
        'controller' => 'Admin:Album@editAction'
    ],
    
    '/%s/module/photogallery/album/save' => [
        'controller' => 'Admin:Album@saveAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/photogallery/album/delete/(:var)' => [
        'controller' => 'Admin:Album@deleteAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/photogallery/browse/album/(:var)' => [
        'controller' => 'Admin:Browser@albumAction'
    ],
    
    '/%s/module/photogallery/browse/album/(:var)/page/(:var)' => [
        'controller' => 'Admin:Browser@albumAction'
    ],
    
    '/%s/module/photogallery' => [
        'controller' => 'Admin:Browser@indexAction'
    ],
    
    '/%s/module/photogallery/browse/(:var)' => [
        'controller' => 'Admin:Browser@indexAction'
    ],
    
    '/%s/module/photogallery/tweak' => [
        'controller' => 'Admin:Photo@tweakAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/photogallery/photo/batch-form' => [
        'controller' => 'Admin:Photo@batchFormAction'
    ],

    '/%s/module/photogallery/photo/batch-upload' => [
        'controller' => 'Admin:Photo@batchUploadAction'
    ],
    
    '/%s/module/photogallery/photo/add' => [
        'controller' => 'Admin:Photo@addAction'
    ],
    
    '/%s/module/photogallery/photo/edit/(:var)' => [
        'controller' => 'Admin:Photo@editAction'
    ],
    
    '/%s/module/photogallery/photo/save' => [
        'controller' => 'Admin:Photo@saveAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/photogallery/photo/delete/(:var)'  => [
        'controller' => 'Admin:Photo@deleteAction',
        'disallow' => ['guest']
    ],
    
    '/%s/module/photogallery/config' => [
        'controller' => 'Admin:Config@indexAction'
    ],
    
    '/%s/module/photogallery/config.ajax' => [
        'controller' => 'Admin:Config@saveAction',
        'disallow' => ['guest']
    ]
];