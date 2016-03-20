<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Photogallery\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;
use Photogallery\Storage\PhotoMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;

final class PhotoMapper extends AbstractMapper implements PhotoMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return 'bono_module_photoalbum_photos';
    }

    /**
     * Returns shared select
     * 
     * @param boolean $published Whether to filter by published records only
     * @param string $albumId Optionally can be filtered by album id
     * @return \Krystal\Db\Sql\Db
     */
    private function getSelectQuery($published, $albumId = null)
    {
        $db = $this->db->select('*')
                       ->from(static::getTableName())
                       ->whereEquals('lang_id', $this->getLangId());

        if ($albumId !== null) {
            $db->andWhereEquals('album_id', $albumId);
        }

        if ($published === true) {
            $db->andWhereEquals('published', '1')
               ->orderBy(new RawSqlFragment('`order`, CASE WHEN `order` = 0 THEN `id` END DESC'));
        } else {
            $db->orderBy('id')
               ->desc();
        }

        return $db;
    }

    /**
     * Fetches a photo name by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id)
    {
        return $this->findColumnByPk($id, 'name');
    }

    /**
     * Delete all photos associated with given album id
     * 
     * @param string $albumId
     * @return boolean
     */
    public function deleteAllByAlbumId($albumId)
    {
        return $this->deleteByColumn('album_id', $albumId);
    }

    /**
     * Deletes a photo by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }

    /**
     * Adds a photo
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function insert(array $input)
    {
        return $this->persist($this->getWithLang($input));
    }

    /**
     * Updates a photo
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        return $this->persist($input);
    }

    /**
     * Count amount of records associated with category id
     * 
     * @param string $albumId
     * @return integer
     */
    public function countAllByAlbumId($albumId)
    {
        return $this->db->select()
                        ->count('id', 'count')
                        ->from(static::getTableName())
                        ->whereEquals('album_id', $albumId)
                        ->query('count');
    }

    /**
     * Fetches photo ids by associated album id
     * 
     * @param string $albumId
     * @return array
     */
    public function fetchPhotoIdsByAlbumId($albumId)
    {
        return $this->db->select('id')
                        ->from(static::getTableName())
                        ->whereEquals('album_id', $albumId)
                        ->queryAll('id');
    }

    /**
     * Fetches a photo by its associated id
     * 
     * @param string $id Photo's id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->findByPk($id);
    }

    /**
     * Updates published state by its associated ids
     * 
     * @param string $id
     * @param string $published
     * @return boolean
     */
    public function updatePublishedById($id, $published)
    {
        return $this->updateColumnByPk($id, 'published', $published);
    }

    /**
     * Updates an order by its associated id
     * 
     * @param string $id
     * @param integer $order
     * @return boolean
     */
    public function updateOrderById($id, $order)
    {
        return $this->updateColumnByPk($id, 'order', $order);
    }

    /**
     * Fetch all records filter by pagination
     * 
     * @param integer $page
     * @param integer $itemsPerPage
     * @param string $albumId Optional album id filter
     * @param boolean $published Whether to filter by published attribute
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage, $albumId = null, $published = false)
    {
        return $this->getSelectQuery($published, $albumId)
                    ->paginate($page, $itemsPerPage)
                    ->queryAll();
    }

    /**
     * Fetches all photos
     * 
     * @param boolean $published Whether to filter by published attribute
     * @param string $albumId Optional album id filter
     * @return array
     */
    public function fetchAll($published, $albumId = null)
    {
        return $this->getSelectQuery($published, $albumId)
                    ->queryAll();
    }
}
