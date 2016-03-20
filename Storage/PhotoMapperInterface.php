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
     * Fetches a photo name by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id);

    /**
     * Delete all records associated with given album id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteAllByAlbumId($albumId);

    /**
     * Updates a record
     * 
     * @param array $data
     * @return boolean
     */
    public function update(array $data);

    /**
     * Inserts a record
     * 
     * @param array $data
     * @return boolean
     */
    public function insert(array $data);

    /**
     * Deletes a photo by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Count amount of records associated with category id
     * 
     * @param string $albumId
     * @return integer
     */
    public function countAllByAlbumId($albumId);

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
     * Updates published state by its associated ids
     * 
     * @param string $id
     * @param string $published
     * @return boolean
     */
    public function updatePublishedById($id, $published);

    /**
     * Updates an order by its associated id
     * 
     * @param string $id
     * @param integer $order
     * @return boolean
     */
    public function updateOrderById($id, $order);

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
