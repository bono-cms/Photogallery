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
        $photos = $this->getPhotoManager()->fetchAllByPage($page, $this->getSharedPerPageCount());
        $url = '/admin/module/photogallery/browse/(:var)';

        return $this->createGrid($photos, $url, null);
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
        $photos = $this->getPhotoManager()->fetchAllByPage($page, $this->getSharedPerPageCount(), $id);
        $url = '/admin/module/photogallery/browse/album/'.$id.'/page/(:var)';

        return $this->createGrid($photos, $url, $id);
    }

    /**
     * Creates a grid
     * 
     * @param array $photos
     * @param string $url
     * @return string
     */
    private function createGrid(array $photos, $url, $albumId)
    {
        $paginator = $this->getPhotoManager()->getPaginator();
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
            'albums' => $this->getAlbumManager()->getAlbumsTree(),
            'paginator' => $paginator,
            'photos' => $photos
        ));
    }

    /**
     * Returns Photo Manager
     * 
     * @return \Photogallery\Service\PhotoManager
     */
    private function getPhotoManager()
    {
        return $this->getModuleService('photoManager');
    }

    /**
     * Returns Album Manager
     * 
     * @return \Photogallery\Service\AlbumManager
     */
    private function getAlbumManager()
    {
        return $this->getModuleService('albumManager');
    }   
}
