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

interface AlbumMapperInterface
{
    /**
     * Fetches album name by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id);

    /**
     * Fetches all albums
     * 
     * @return array
     */
    public function fetchAll();

    /**
     * Deletes an album by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Fetches breadcrumb data
     * 
     * @return array
     */
    public function fetchBcData();

    /**
     * Fetches children by parent id
     * 
     * @param string $parentId
     * @param mixed $limit Optional limit to be applied
     * @return array
     */
    public function fetchChildrenByParentId($parentId, $limit = null);;

    /**
     * Fetches a record by its id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations);
}
