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
            self::column('lang_id', self::getTranslationTable()),
            self::column('name', self::getTranslationTable()),
            self::column('description', self::getTranslationTable()),
            self::column('date'),
            self::column('published'),
            self::column('order'),
            self::column('photo'),
            self::column('album_id'),
            self::column('id')
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
            array(AlbumMapper::column('name', AlbumMapper::getTranslationTable()) => 'album')
        );

        $db = $this->db->select($columns)
                       ->from(self::getTableName())
                       // Translation relation
                       ->innerJoin(self::getTranslationTable())
                       ->on()
                       ->equals(
                            self::column('id', self::getTranslationTable()),
                            new RawSqlFragment(self::column('id'))
                        )
                        // Category translation
                        ->innerJoin(AlbumMapper::getTranslationTable())
                        ->on()
                        ->equals(
                            self::column('album_id'),
                            new RawSqlFragment(AlbumMapper::column('id', AlbumMapper::getTranslationTable()))
                        )
                        ->rawAnd()
                        ->equals(
                            AlbumMapper::column('lang_id', AlbumMapper::getTranslationTable()),
                            new RawSqlFragment(self::column('lang_id', self::getTranslationTable()))
                        )
                        // Category relation
                        ->innerJoin(AlbumMapper::getTableName())
                        ->on()
                        ->equals(
                            AlbumMapper::column('id'),
                            new RawSqlFragment(AlbumMapper::column('id', AlbumMapper::getTranslationTable()))
                        )
                        // Filtering condition
                        ->whereEquals(
                            self::column('lang_id', self::getTranslationTable()), 
                            $this->getLangId()
                        );

        if ($albumId !== null) {
            $db->andWhereEquals('album_id', $albumId);
        }

        if ($published === true) {
            $db->andWhereEquals(self::column('published'), '1')
               ->orderBy(
                new RawSqlFragment(sprintf('%s, CASE WHEN %s = 0 THEN %s END DESC', 
                    self::column('order'), 
                    self::column('order'), 
                    self::column('id')))
                );
        } else {
            $db->orderBy(self::column('id'))
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
     * Update settings
     * 
     * @param array $settings
     * @return boolean
     */
    public function updateSettings(array $settings)
    {
        return $this->updateColumns($settings, array('order', 'published'));
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
