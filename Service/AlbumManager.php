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

use Photogallery\Storage\AlbumMapperInterface;
use Photogallery\Storage\PhotoMapperInterface;
use Cms\Service\HistoryManagerInterface;
use Cms\Service\AbstractManager;
use Cms\Service\WebPageManagerInterface;
use Krystal\Stdlib\ArrayUtils;
use Krystal\Image\Tool\ImageManagerInterface;
use Krystal\Security\Filter;
use Krystal\Tree\AdjacencyList\TreeBuilder;
use Krystal\Tree\AdjacencyList\BreadcrumbBuilder;
use Krystal\Tree\AdjacencyList\Render;

final class AlbumManager extends AbstractManager implements AlbumManagerInterface
{
    /**
     * Any-compliant album mapper
     * 
     * @var \Photogallery\Storage\AlbumMapperInterface
     */
    private $albumMapper;

    /**
     * Any-compliant image mapper
     * 
     * @var \Photogallery\Storage\PhotoMapperInterface
     */
    private $photoMapper;

    /**
     * History manager
     * 
     * @var \Cms\Service\HistoryManagerInterface
     */
    private $historyManager;

    /**
     * Image manager to deal with images removal
     * 
     * @var \Krystal\Image\ImageManagerInterface
     */
    private $imageManager;

    /**
     * Album photo manager
     * 
     * @var \Krystal\Image\ImageManagerInterface
     */
    private $albumPhoto;

    /**
     * Web page manager to handle slugs
     * 
     * @var \Cms\Service\WebPageManagerInterface
     */
    private $webPageManager;

    /**
     * State initialization
     * 
     * @param \Album\Storage\AlbumMapperInterface $albumMapper
     * @param \Album\Storage\PhotoMapperInterface $photoMapper
     * @param \Krystal\Image\ImageManagerInterface $imageManager
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @param \Cms\Service\HistoryManagerInterface $historyManager
     * @param \Menu\Service\MenuWidgetInterface $menuWidget Optional menu widget service
     * @return void
     */
    public function __construct(
        AlbumMapperInterface $albumMapper, 
        PhotoMapperInterface $photoMapper, 
        ImageManagerInterface $albumPhoto,
        ImageManagerInterface $imageManager, 
        WebPageManagerInterface $webPageManager, 
        HistoryManagerInterface $historyManager
    ){
        $this->albumMapper = $albumMapper;
        $this->photoMapper = $photoMapper;
        $this->albumPhoto = $albumPhoto;
        $this->imageManager = $imageManager;
        $this->webPageManager = $webPageManager;
        $this->historyManager = $historyManager;
    }

    /**
     * Returns a collection of switching URLs
     * 
     * @param string $id Page ID
     * @return array
     */
    public function getSwitchUrls($id)
    {
        return $this->albumMapper->createSwitchUrls($id, 'Photogallery', 'Photogallery:Album@showAction');
    }

    /**
     * Returns a tree pre-pending prompt message
     * 
     * @param string $text
     * @return array
     */
    public function getPromtWithAlbumsTree($text)
    {
        $tree = $this->getAlbumsTree(false);
        ArrayUtils::assocPrepend($tree, null, $text);

        return $tree;
    }

    /**
     * Returns albums tree
     * 
     * @param boolean $all Whether to fetch as a pair or a collection
     * @return array
     */
    public function getAlbumsTree($all)
    {
        $rows = $this->albumMapper->fetchAll();

        $treeBuilder = new TreeBuilder($rows);

        if ($all == true) {
            $rows = $treeBuilder->render(new Render\Merge('name'));

            // @TODO XSS filtering
            foreach ($rows as $index => $row) {
                // Append new "url" key
                $rows[$index]['url'] = $this->webPageManager->surround($row['slug'], $row['lang_id']);
            }

            return $rows;
        } else {
            return $treeBuilder->render(new Render\PhpArray('name'));
        }
    }

    /**
     * Returns breadcrumbs
     * 
     * @param \Photogallery\Service\AlbumEntity $album
     * @return array
     */
    public function getBreadcrumbs(AlbumEntity $album)
    {
        return $this->getBreadcrumbsById($album->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function fetchNameByWebPageId($webPageId)
    {
        return $this->albumMapper->fetchNameByWebPageId($webPageId);
    }

    /**
     * Fetches children by parent id
     * 
     * @param string $parentId
     * @return array
     */
    public function fetchChildrenByParentId($parentId)
    {
        return $this->prepareResults($this->albumMapper->fetchChildrenByParentId($parentId));
    }

    /**
     * Fetches all albums
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->albumMapper->fetchAll();
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $album)
    {
        $imageBag = clone $this->albumPhoto->getImageBag();
        $imageBag->setId((int) $album['id'])
                 ->setCover($album['cover']);

        $entity = new AlbumEntity();
        $entity->setId($album['id'], AlbumEntity::FILTER_INT)
               ->setParentId($album['parent_id'], AlbumEntity::FILTER_INT)
               ->setLangId($album['lang_id'], AlbumEntity::FILTER_INT)
               ->setWebPageId($album['web_page_id'], AlbumEntity::FILTER_INT)
               ->setTitle($album['title'], AlbumEntity::FILTER_HTML)
               ->setName($album['name'], AlbumEntity::FILTER_HTML)
               ->setDescription($album['description'], AlbumEntity::FILTER_SAFE_TAGS)
               ->setOrder($album['order'], AlbumEntity::FILTER_INT)
               ->setKeywords($album['keywords'], AlbumEntity::FILTER_HTML)
               ->setSlug($album['slug'])
               ->setUrl($this->webPageManager->surround($entity->getSlug(), $album['lang_id']))
               ->setPermanentUrl('/module/photogallery/'.$entity->getId())
               ->setMetaDescription($album['meta_description'], AlbumEntity::FILTER_HTML)
               ->setSeo($album['seo'], AlbumEntity::FILTER_BOOL)
               ->setCover($album['cover'])
               ->setImageBag($imageBag);

        return $entity;
    }

    /**
     * Returns last album's id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->albumMapper->getLastId();
    }

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->albumMapper->getPaginator();
    }

    /**
     * Fetches all albums filtered by pagination
     * 
     * @param integer $page Current page
     * @param integer $itemsPerPage Items per page count
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage)
    {
        return $this->prepareResults($this->albumMapper->fetchAll($page, $itemsPerPage));
    }

    /**
     * Saves a page
     * 
     * @param array $input
     * @return boolean
     */
    private function savePage(array $input)
    {
        $album =& $input['data']['album'];

        // Strict casting
        $album['parent_id'] = (int) $album['parent_id'];
        $album['order'] = (int) $album['order'];

        $album = ArrayUtils::arrayWithout($album, array('slug', 'remove_cover'));
        return $this->albumMapper->savePage('Photogallery', 'Photogallery:Album@showAction', $album, $input['data']['translation']);
    }

    /**
     * Adds an album
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        // References
        $album =& $input['data']['album'];
        $file = isset($input['files']['file']) ? $input['files']['file'] : false;

        // If image file is selected
        if ($file) {
            // And finally append
            $album['cover'] = $file->getUniqueName();
        }

        #$this->track('Album "%s" has been created', $form['name']);
        $this->savePage($input);

        if ($file) {
            $this->albumPhoto->upload($this->getLastId(), $file);
        }

        return true;
    }

    /**
     * Updates an album
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        $album =& $input['data']['album'];

        // Allow to remove a cover, only it case it exists and checkbox was checked
        if (isset($category['remove_cover'])) {
            // Remove a cover, but not a dir itself
            $this->albumPhoto->delete($album['id']);
            $album['cover'] = '';
        } else {
            if (!empty($input['files']['file'])) {
                $file =& $input['files']['file'];
                // If we have a previous cover's image, then we need to remove it
                if (!empty($album['cover'])) {
                    if (!$this->albumPhoto->delete($album['id'], $album['cover'])) {
                        // If failed, then exit this method immediately
                        return false;
                    }
                }

                $album['cover'] = $file->getUniqueName();
                $this->albumPhoto->upload($album['id'], $file);
            }
        }

        #$this->track('Category "%s" has been updated', $category['name']);
        return $this->savePage($input);
    }

    /**
     * Deletes a whole album by its id including all its photos
     * 
     * @param string $id Album's id
     * @return boolean
     */
    public function deleteById($id)
    {
        // Save the name into a variable, before an album is removed
        $name = Filter::escape($this->albumMapper->fetchNameById($id));

        // Do remove now
        $this->removeAlbumById($id);
        $this->removeChildAlbumsByParentId($id);

        $this->track('The album "%s" has been removed', $name);

        return true;
    }

    /**
     * Removes child albums that belong to provided id
     * 
     * @param string $parentId
     * @return boolean
     */
    private function removeChildAlbumsByParentId($parentId)
    {
        $treeBuilder = new TreeBuilder($this->albumMapper->fetchAll());
        $ids = $treeBuilder->findChildNodeIds($parentId);

        // If there's at least one child id, then start working next
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $this->removeAlbumById($id);
            }
        }

        return true;
    }

    /**
     * Removes an album by its associated id
     * 
     * @param string $albumId
     * @return boolean
     */
    private function removeAlbumById($albumId)
    {
        $this->albumMapper->deletePage($albumId);

        // Grab all photos associated with target album id
        $photosIds = $this->photoMapper->fetchPhotoIdsByAlbumId($albumId);

        // Do batch removal if album has at least one photo
        if (!empty($photosIds)) {
            foreach ($photosIds as $photoId) {
                // Remove a photo
                $this->imageManager->delete($photoId) && $this->photoMapper->deleteById($photoId);
            }
        }

        $this->albumPhoto->delete($albumId);
        return true;
    }

    /**
     * Fetches album entity by its id
     * 
     * @param string $id Album's id
     * @param boolean $withTranslations Whether to fetch translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations == true) {
            return $this->prepareResults($this->albumMapper->fetchById($id, true));
        } else {
            return $this->prepareResult($this->albumMapper->fetchById($id, false));
        }
    }

    /**
     * Tracks activity
     * 
     * @param string $message
     * @param string $placeholder
     * @return boolean
     */
    private function track($message, $placeholder)
    {
        return $this->historyManager->write('Photogallery', $message, $placeholder);
    }

    /**
     * Gets all breadcrumbs by associated id
     * 
     * @param string $id Category id
     * @return array
     */
    private function getBreadcrumbsById($id)
    {
        $wm = $this->webPageManager;
        $builder = new BreadcrumbBuilder($this->albumMapper->fetchBcData(), $id);

        return $builder->makeAll(function($breadcrumb) use ($wm) {
            return array(
                'name' => $breadcrumb['name'],
                'link' => $wm->getUrl($breadcrumb['web_page_id'], $breadcrumb['lang_id'])
            );
        });
    }   
}
