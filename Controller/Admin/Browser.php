<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Photogallery\Controller\Admin;

use Cms\Controller\Admin\AbstractController;

final class Browser extends AbstractController
{
    /**
     * Renders a grid
     * 
     * @param integer $page
     * @return string
     */
    public function indexAction($page = 1)
    {
        $url = $this->createUrl('Photogallery:Admin:Browser@indexAction', array(), 1);
        return $this->createGrid($page, $url, null);
    }

    /**
     * Filters photos by album id
     * 
     * @param string $id
     * @param integer $page
     * @return string
     */
    public function albumAction($id, $page = 1)
    {
        $url = $this->createUrl('Photogallery:Admin:Browser@albumAction', array($id), 1);
        return $this->createGrid($page, $url, $id);
    }

    /**
     * Creates a grid
     * 
     * @param integer $page Current page
     * @param string $url
     * @param string $albumId Album id filter
     * @return string
     */
    private function createGrid($page, $url, $albumId)
    {
        $photoManager = $this->getModuleService('photoManager');
        $photos = $photoManager->fetchAllByPage($page, $this->getSharedPerPageCount(), $albumId);

        $paginator = $photoManager->getPaginator();
        $paginator->setUrl($url);

        // Load view plugins
        $this->view->getPluginBag()
                   ->appendScript('@Photogallery/admin/browser.js')
                   ->load('zoom');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Photogallery');

        return $this->view->render('browser', array(
            'albumId' => $albumId,
            'taskManager' => $this->getModuleService('taskManager'),
            'albums' => $this->getModuleService('albumManager')->getAlbumsTree(),
            'paginator' => $paginator,
            'photos' => $photos
        ));
    }
}
