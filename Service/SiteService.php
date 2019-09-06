<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Photogallery\Service;

final class SiteService
{
    /**
     * Photo manager service
     * 
     * @var \Photogallery\Service\PhotoManager
     */
    private $photoManager;

    /**
     * Album manager service
     * 
     * @var \Photogallery\Service\AlbumManager
     */
    private $albumManager;

    /**
     * State initialization
     * 
     * @param \Photogallery\Service\PhotoManager $photoManager
     * @param \Photogallery\Service\AlbumManager $albumManager
     * @return void
     */
    public function __construct(PhotoManager $photoManager, AlbumManager $albumManager)
    {
        $this->photoManager = $photoManager;
        $this->albumManager = $albumManager;
    }

    /**
     * Returns child album entities
     * 
     * @param string $id Parent album id
     * @param mixed $limit Optional limit to be applied
     * @return array
     */
    public function getChildAlbums($id, $limit = null)
    {
        return $this->albumManager->fetchChildrenByParentId($id, $limit);
    }

    /**
     * Returns all photo entities by associated album id
     * 
     * @param string $id Album id
     * @return array
     */
    public function getAllByAlbumId($id)
    {
        return $this->photoManager->fetchAll(true, $id);
    }
}
