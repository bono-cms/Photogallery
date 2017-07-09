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
use Cms\Storage\MySQL\WebPageMapper;
use Photogallery\Storage\AlbumMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;

final class AlbumMapper extends AbstractMapper implements AlbumMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_photoalbum_albums');
    }

    /**
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return self::getWithPrefix('bono_module_photoalbum_albums_translations');
    }

    /**
     * Returns a collection of shared columns to be selected
     * 
     * @param boolean $all Whether to select all columns or not
     * @return array
     */
    private function getSharedColumns($all)
    {
        // Basic columns to be selected
        $columns = array(
            self::getFullColumnName('id'),
            self::getFullColumnName('parent_id'),
            self::getFullColumnName('web_page_id', self::getTranslationTable()),
            self::getFullColumnName('lang_id', self::getTranslationTable()),
            self::getFullColumnName('name', self::getTranslationTable()),
            self::getFullColumnName('seo'),
            self::getFullColumnName('cover'),
            self::getFullColumnName('order'),
            WebPageMapper::getFullColumnName('slug'),
        );

        if ($all) {
            $columns = array_merge($columns, array(
                self::getFullColumnName('title', self::getTranslationTable()),
                self::getFullColumnName('description', self::getTranslationTable()),
                self::getFullColumnName('keywords', self::getTranslationTable()),
                self::getFullColumnName('meta_description', self::getTranslationTable()),
            ));
        }

        return $columns;
    }

    /**
     * Fetches breadcrumb data
     * 
     * @return array
     */
    public function fetchBcData()
    {
        return $this->db->select($this->getSharedColumns(false))
                        ->from(self::getTableName())
                        // Translation relation
                        ->innerJoin(self::getTranslationTable())
                        ->on()
                        ->equals(
                            self::getFullColumnName('id', self::getTranslationTable()), 
                            new RawSqlFragment(self::getFullColumnName('id'))
                        )
                        // Web page relation
                        ->innerJoin(WebPageMapper::getTableName())
                        ->on()
                        ->equals(
                            WebPageMapper::getFullColumnName('id'),
                            new RawSqlFragment(self::getFullColumnName('web_page_id', self::getTranslationTable()))
                        )
                        ->rawAnd()
                        ->equals(
                            WebPageMapper::getFullColumnName('lang_id'),
                            new RawSqlFragment(self::getFullColumnName('lang_id', self::getTranslationTable()))
                        )
                        // Filtering condition
                        ->whereEquals(self::getFullColumnName('lang_id', self::getTranslationTable()), $this->getLangId())
                        ->queryAll();
    }

    /**
     * Fetches child albums by parent id
     * 
     * @param string $parentId
     * @return array
     */
    public function fetchChildrenByParentId($parentId)
    {
        return $this->createWebPageSelect($this->getSharedColumns(true))
                    // Filtering condition
                    ->whereEquals(self::getFullColumnName('parent_id'), $parentId)
                    ->andWhereEquals(self::getFullColumnName('lang_id', self::getTranslationTable()), $this->getLangId())
                    ->queryAll();
    }

    /**
     * Fetches a record by its id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        return $this->findWebPage($this->getSharedColumns(true), $id, $withTranslations);
    }

    /**
     * Fetches all albums
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Items Per page count
     * @return array
     */
    public function fetchAll($page = null, $itemsPerPage = null)
    {
        // Whether pagination is required
        $needsPagination = $page !== null && $itemsPerPage !== null;

        $countOnlyPublished = false;
        $columns = $this->getSharedColumns($needsPagination);

        $db = $this->db->select($columns)
                        ->count(PhotoMapper::getFullColumnName('id'), 'photos_count')
                        ->from(self::getTableName())

                        // Album relation
                        ->leftJoin(PhotoMapper::getTableName())
                        ->on()
                        ->equals(
                            self::getFullColumnName('id'), 
                            new RawSqlFragment(PhotoMapper::getFullColumnName('album_id'))
                        )
                        // Translation relation
                        ->innerJoin(self::getTranslationTable())
                        ->on()
                        ->equals(
                            self::getFullColumnName('id', self::getTranslationTable()),
                            new RawSqlFragment(self::getFullColumnName('id'))
                        )
                        ->rawAnd()
                        ->equals(
                            self::getFullColumnName('lang_id', self::getTranslationTable()),
                            $this->getLangId()
                        )
                        // Web page relation
                        ->innerJoin(WebPageMapper::getTableName())
                        ->on()
                        ->equals(
                            WebPageMapper::getFullColumnName('id'),
                            new RawSqlFragment(self::getFullColumnName('web_page_id', self::getTranslationTable()))
                        )
                        ->rawAnd()
                        ->equals(
                            WebPageMapper::getFullColumnName('lang_id'),
                            new RawSqlFragment(self::getFullColumnName('lang_id', self::getTranslationTable()))
                        );

        if ($countOnlyPublished == true) {
            $db->whereEquals(PhotoMapper::getFullColumnName('published'), '1');
        }

        // Aggregate grouping
        $db->groupBy($columns);

        // Optional pagination
        if ($needsPagination) {
            $db->paginate($page, $itemsPerPage);
        }

        return $db->queryAll();
    }

    /**
     * Fetches album name by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function fetchNameById($id)
    {
    }

    /**
     * Deletes an album by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }
}
