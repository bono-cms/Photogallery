<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Photogallery\Storage;

interface PhotoMapperInterface
{
    /**
     * Delete all records associated with given album id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteAllByAlbumId($albumId);

    /**
     * Fetches photo ids by associated album id
     * 
     * @param string $albumId
     * @return array
     */
    public function fetchPhotoIdsByAlbumId($albumId);

    /**
     * Fetches a record by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id);

    /**
     * Update settings
     * 
     * @param array $settings
     * @return boolean
     */
    public function updateSettings(array $settings);

    /**
     * Fetch all records filter by pagination
     * 
     * @param integer $page
     * @param integer $itemsPerPage
     * @param string $albumId Optional album id filter
     * @param boolean $published Whether to filter by published attribute
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage, $albumId = null, $published = false);

    /**
     * Fetches all photos
     * 
     * @param boolean $published Whether to filter by published attribute
     * @param string $albumId Optional album id filter
     * @return array
     */
    public function fetchAll($published, $albumId = null);
}
