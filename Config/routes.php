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
        'controller' => 'Admin:Album:Add@indexAction'
    ),

    '/admin/module/photogallery/album/add.ajax' => array(
        'controller' => 'Admin:Album:Add@addAction',
        'disallow' => array('guest')
    ),

    '/admin/module/photogallery/album/edit/(:var)' => array(
        'controller' => 'Admin:Album:Edit@indexAction'
    ),
    
    '/admin/module/photogallery/album/edit.ajax' => array(
        'controller' => 'Admin:Album:Edit@updateAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/album/delete.ajax' => array(
        'controller' => 'Admin:Browser@deleteAlbumAction',
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
    
    '/admin/module/photogallery/save.ajax' => array(
        'controller' => 'Admin:Browser@saveAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/delete-selected.ajax' => array(
        'controller' => 'Admin:Browser@deleteSelectedAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/photo/add' => array(
        'controller' => 'Admin:Photo:Add@indexAction'
    ),
    
    '/admin/module/photogallery/photo/add.ajax' => array(
        'controller' => 'Admin:Photo:Add@addAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/photo/edit/(:var)' => array(
        'controller' => 'Admin:Photo:Edit@indexAction'
    ),
    
    '/admin/module/photogallery/photo/edit.ajax' => array(
        'controller' => 'Admin:Photo:Edit@updateAction',
        'disallow' => array('guest')
    ),
    
    '/admin/module/photogallery/photo/delete.ajax'  => array(
        'controller' => 'Admin:Browser@deleteAction',
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
