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

/**
 * API for PhotoManager
 */
interface PhotoManagerInterface
{
    /**
     * Update settings
     * 
     * @param array $settings
     * @return boolean
     */
    public function updateSettings(array $settings);

    /**
     * Removes photos by their associated ids
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids);

    /**
     * Removes a photo by its associated id
     * 
     * @param string $id Photo id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator();

    /**
     * Returns last photo id
     * 
     * @return integer
     */
    public function getLastId();

    /**
     * Adds a photo
     * 
     * @param array $form Form data
     * @return boolean
     */
    public function add(array $form);

    /**
     * Updates a photo
     * 
     * @param array $form Form data
     * @return boolean
     */
    public function update(array $form);

    /**
     * Fetches a photo bag by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translation or not
     * @return array
     */
    public function fetchById($id, $withTranslations = true);

    /**
     * Fetches all photos filtered by pagination
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page count
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
