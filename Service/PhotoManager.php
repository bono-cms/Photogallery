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

use Cms\Service\AbstractManager;
use Cms\Service\HistoryManagerInterface;
use Photogallery\Storage\PhotoMapperInterface;
use Photogallery\Storage\AlbumMapperInterface;
use Krystal\Image\Tool\ImageManagerInterface;
use Krystal\Security\Filter;

final class PhotoManager extends AbstractManager implements PhotoManagerInterface
{
    /**
     * Any compliant photo mapper
     * 
     * @var \Photogallery\Storage\PhotoMapperInterface
     */
    private $photoMapper;

    /**
     * Any compliant album mapper
     * 
     * @var \Photogallery\Storage\AlbumMapperInterface
     */
    private $albumMapper;

    /**
     * Handles removal, uploading and building image paths
     * 
     * @var \Krystal\Image\ImageManagerInterface
     */
    private $imageManager;

    /**
     * History manager is used to keep track of common actions
     * 
     * @var \Photogallery\Storage\HistoryManagerInterface
     */
    private $historyManager;

    /**
     * State initialization
     * 
     * @param \Photogallery\Storage\PhotoMapperInterface $photoMapper
     * @param \Photogallery\Storage\AlbumMapperInterface $albumMapper
     * @param \Krystal\Image\ImageManagerInterface $imageManager
     * @param \Cms\Service\HistoryManagerInterface $historyManager
     * @return void
     */
    public function __construct(
        PhotoMapperInterface $photoMapper, 
        AlbumMapperInterface $albumMapper, 
        ImageManagerInterface $imageManager,
        HistoryManagerInterface $historyManager
    ){
        $this->photoMapper  = $photoMapper;
        $this->albumMapper  = $albumMapper;
        $this->imageManager = $imageManager;
        $this->historyManager = $historyManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $photo)
    {
        $imageBag = clone $this->imageManager->getImageBag();
        $imageBag->setId((int) $photo['id'])
                 ->setCover($photo['photo']);

        $entity = new PhotoEntity();
        $entity->setImageBag($imageBag)
                 ->setId($photo['id'], PhotoEntity::FILTER_INT)
                 ->setLangId($photo['lang_id'], PhotoEntity::FILTER_INT)
                 ->setName($photo['name'], PhotoEntity::FILTER_HTML)
                 ->setAlbumId($photo['album_id'], PhotoEntity::FILTER_INT)
                 ->setAlbumName(isset($photo['album']) ? $photo['album'] : null, PhotoEntity::FILTER_HTML)
                 ->setPhoto($photo['photo'], PhotoEntity::FILTER_HTML)
                 ->setDescription($photo['description'], PhotoEntity::FILTER_HTML)
                 ->setOrder($photo['order'], PhotoEntity::FILTER_INT)
                 ->setPublished($photo['published'], PhotoEntity::FILTER_BOOL)
                 ->setDate(date('d/m/y', $photo['date']));

        return $entity;
    }

    /**
     * Update settings
     * 
     * @param array $settings
     * @return boolean
     */
    public function updateSettings(array $settings)
    {
        return $this->photoMapper->updateSettings($settings);
    }

    /**
     * Deletes a photo by its associated id
     * 
     * @param string $id
     * @return boolean
     */
    private function delete($id)
    {
        return $this->imageManager->delete($id) && $this->photoMapper->deleteEntity($id);
    }

    /**
     * Removes photos by their associated ids
     * 
     * @param array $ids
     * @return boolean
     */
    public function deleteByIds(array $ids)
    {
        foreach ($ids as $id) {
            if (!$this->delete($id)) {
                return false;
            }
        }

        $this->track('Batch removal of %s photos', count($ids));
        return true;
    }

    /**
     * Removes a photo by its associated id
     * 
     * @param string $id Photo's id
     * @return boolean
     */
    public function deleteById($id)
    {
        #$name = Filter::escape($this->photoMapper->fetchNameById($id));

        if ($this->delete($id)) {
            #$this->track('The photo "%s" has been removed', $name);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->photoMapper->getPaginator();
    }

    /**
     * Returns last photo id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->photoMapper->getLastId();
    }

    /**
     * Prepares a container before sending to a mapper
     * 
     * @param array $input Raw input data
     * @return array
     */
    private function prepareInput(array $input)
    {
        $data =& $input['data']['photo'];
        $file =& $input['files']['file'];

        // Empty photo name should be replace by target filename
        if (empty($data['name'])) {
            $data['name'] = pathinfo($file[0]->getName(), \PATHINFO_FILENAME);
        }

        $this->filterFileInput($file);

        return $input;
    }

    /**
     * Adds a photo
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        $input = $this->prepareInput($input);

        $data =& $input['data']['photo'];
        $file =& $input['files']['file'];
        $translations =& $input['data']['translation'];

        $data['photo'] = $file[0]->getName();
        $data['date'] = time();

        #$this->track('A new photo "%s" has been uploaded', $data['name']);
        $this->photoMapper->saveEntity($data, $translations);

        // Insert must be first, so that we can get the last id
        return $this->imageManager->upload($this->getLastId(), $file);
    }

    /**
     * Updates a photo
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function update(array $input)
    {
        $data =& $input['data']['photo'];
        $translations =& $input['data']['translation'];

        // Upload a photo if present and override it
        if (!empty($input['files'])) {
            $input = $this->prepareInput($input);
            $file =& $input['files']['file'];

            // First of all, we need to remove old photo on the file-system
            if ($this->imageManager->delete($data['id'], $data['photo'])) {
                // And now upload a new one
                $data['photo'] = $file[0]->getName();
                $this->imageManager->upload($data['id'], $file);

            } else {
                return false;
            }
        }

        #$this->track('The photo "%s" has been updated', $data['name']);
        return $this->photoMapper->saveEntity($data, $translations);
    }

    /**
     * Fetches a photo bag by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id)
    {
        return $this->prepareResults($this->photoMapper->fetchById($id));
    }

    /**
     * Fetches all photos filtered by pagination
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Items per page count
     * @param string $albumId Optional album id filter
     * @param boolean $published Whether to filter by published attribute
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage, $albumId = null, $published = false)
    {
        return $this->prepareResults($this->photoMapper->fetchAllByPage($page, $itemsPerPage, $albumId, $published));
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
        return $this->prepareResults($this->photoMapper->fetchAll($published, $albumId));
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
}
