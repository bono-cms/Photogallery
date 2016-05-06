<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return array(
    '/module/photogallery/(:var)' => array(
        'controller' => 'Album@showAction',
    ),
    
    '/%s/module/photogallery/album/add' => array(
        'controller' => 'Admin:Album@addAction'
    ),

    '/%s/module/photogallery/album/edit/(:var)' => array(
        'controller' => 'Admin:Album@editAction'
    ),
    
    '/%s/module/photogallery/album/save' => array(
        'controller' => 'Admin:Album@saveAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/photogallery/album/delete/(:var)' => array(
        'controller' => 'Admin:Album@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/photogallery/browse/album/(:var)' => array(
        'controller' => 'Admin:Browser@albumAction'
    ),
    
    '/%s/module/photogallery/browse/album/(:var)/page/(:var)' => array(
        'controller' => 'Admin:Browser@albumAction'
    ),
    
    '/%s/module/photogallery' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/%s/module/photogallery/browse/(:var)' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/%s/module/photogallery/tweak' => array(
        'controller' => 'Admin:Photo@tweakAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/photogallery/photo/add' => array(
        'controller' => 'Admin:Photo@addAction'
    ),
    
    '/%s/module/photogallery/photo/edit/(:var)' => array(
        'controller' => 'Admin:Photo@editAction'
    ),
    
    '/%s/module/photogallery/photo/save' => array(
        'controller' => 'Admin:Photo@saveAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/photogallery/photo/delete/(:var)'  => array(
        'controller' => 'Admin:Photo@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/photogallery/config' => array(
        'controller' => 'Admin:Config@indexAction'
    ),
    
    '/%s/module/photogallery/config.ajax' => array(
        'controller' => 'Admin:Config@saveAction',
        'disallow' => array('guest')
    )
);
