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
    
    '/admin/module/photogallery/album/add' => array(
        'controller' => 'Admin:Album@addAction'
    ),

    '/admin/module/photogallery/album/edit/(:var)' => array(
        'controller' => 'Admin:Album@editAction'
    ),
    
    '/admin/module/photogallery/album/save' => array(
        'controller' => 'Admin:Album@saveAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/album/delete' => array(
        'controller' => 'Admin:Album@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/browse/album/(:var)' => array(
        'controller' => 'Admin:Browser@albumAction'
    ),
    
    '/admin/module/photogallery/browse/album/(:var)/page/(:var)' => array(
        'controller' => 'Admin:Photo:Browser@albumAction'
    ),
    
    '/admin/module/photogallery' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/admin/module/photogallery/browse/(:var)' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/admin/module/photogallery/tweak' => array(
        'controller' => 'Admin:Photo@tweakAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/photo/add' => array(
        'controller' => 'Admin:Photo@addAction'
    ),
    
    '/admin/module/photogallery/photo/edit/(:var)' => array(
        'controller' => 'Admin:Photo@editAction'
    ),
    
    '/admin/module/photogallery/photo/save' => array(
        'controller' => 'Admin:Photo@saveAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/photo/delete'  => array(
        'controller' => 'Admin:Photo@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/config' => array(
        'controller' => 'Admin:Config@indexAction'
    ),
    
    '/admin/module/photogallery/config.ajax' => array(
        'controller' => 'Admin:Config@saveAction',
        'disallow' => array('guest')
    )
);
