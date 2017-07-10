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
        return self::getWithPrefix('bono_module_photoalbum_photos');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return self::getWithPrefix('bono_module_photoalbum_photos_translations');
    }

    /**
     * Returns a collection of shared columns
     * 
     * @return array
     */
    private function getSharedColumns()
    {
        return array(
            self::getFullColumnName('lang_id', self::getTranslationTable()),
            self::getFullColumnName('name', self::getTranslationTable()),
            self::getFullColumnName('description', self::getTranslationTable()),
            self::getFullColumnName('date'),
            self::getFullColumnName('published'),
            self::getFullColumnName('order'),
            self::getFullColumnName('photo'),
            self::getFullColumnName('album_id'),
            self::getFullColumnName('id')
        );
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
        // Columns to be selected
        $columns = array_merge(
            $this->getSharedColumns(), 
            array(AlbumMapper::getFullColumnName('name', AlbumMapper::getTranslationTable()) => 'album')
        );

        $db = $this->db->select($columns)
                       ->from(self::getTableName())
                       // Translation relation
                       ->innerJoin(self::getTranslationTable())
                       ->on()
                       ->equals(
                            self::getFullColumnName('id', self::getTranslationTable()),
                            new RawSqlFragment(self::getFullColumnName('id'))
                        )
                        // Category translation
                        ->innerJoin(AlbumMapper::getTranslationTable())
                        ->on()
                        ->equals(
                            self::getFullColumnName('album_id'),
                            new RawSqlFragment(AlbumMapper::getFullColumnName('id', AlbumMapper::getTranslationTable()))
                        )
                        ->rawAnd()
                        ->equals(
                            AlbumMapper::getFullColumnName('lang_id', AlbumMapper::getTranslationTable()),
                            new RawSqlFragment(self::getFullColumnName('lang_id', self::getTranslationTable()))
                        )
                        // Category relation
                        ->innerJoin(AlbumMapper::getTableName())
                        ->on()
                        ->equals(
                            AlbumMapper::getFullColumnName('id'),
                            new RawSqlFragment(AlbumMapper::getFullColumnName('id', AlbumMapper::getTranslationTable()))
                        )
                        // Filtering condition
                        ->whereEquals(
                            self::getFullColumnName('lang_id', self::getTranslationTable()), 
                            $this->getLangId()
                        );

        if ($albumId !== null) {
            $db->andWhereEquals('album_id', $albumId);
        }

        if ($published === true) {
            $db->andWhereEquals(self::getFullColumnName('published'), '1')
               ->orderBy(new RawSqlFragment('`order`, CASE WHEN `order` = 0 THEN `id` END DESC'));
        } else {
            $db->orderBy(self::getFullColumnName('id'))
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
        return $this->findEntity($this->getSharedColumns(), $id, true);
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
