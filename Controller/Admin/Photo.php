<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Photogallery\Controller\Admin;

use Cms\Controller\Admin\AbstractController;
use Krystal\Validate\Pattern;
use Krystal\Stdlib\VirtualEntity;

final class Photo extends AbstractController
{
    /**
     * Returns photo manager
     * 
     * @return \Photogallery\Service\PhotoManager
     */
    private function getPhotoManager()
    {
        return $this->getModuleService('photoManager');
    }

    /**
     * Creates a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $photo
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $photo, $title)
    {
        // Load view plugins
        $this->view->getPluginBag()
                   ->appendScript('@Photogallery/admin/photo.form.js');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Photogallery', 'Photogallery:Admin:Browser@indexAction')
                                       ->addOne($title);

        return $this->view->render('photo.form', array(
            'albums' => $this->getModuleService('albumManager')->getAlbumsTree(),
            'photo' => $photo
        ));
    }

    /**
     * Renders empty form
     * 
     * @return string
     */
    public function addAction()
    {
        $this->view->getPluginBag()
                   ->load('preview');

        $photo = new VirtualEntity();
        $photo->setPublished(true)
              ->setOrder(0);

        return $this->createForm($photo, 'Add a photo');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $photo = $this->getPhotoManager()->fetchById($id);

        if ($photo !== false) {
            $this->view->getPluginBag()
                       ->load('zoom');

            return $this->createForm($photo, 'Edit the photo');
        } else {
            return false;
        }
    }

    /**
     * Save changes
     * 
     * @return string
     */
    public function tweakAction()
    {
        if ($this->request->hasPost('published', 'order')) {
            $published = $this->request->getPost('published');
            $orders = $this->request->getPost('order');

            // Grab a service
            $photoManager = $this->getPhotoManager();

            if ($photoManager->updatePublished($published) && $photoManager->updateOrders($orders)) {
                $this->flashBag->set('success', 'Settings have been updated successfully');
                return '1';
            }
        }
    }

    /**
     * Deletes a photo by its associated id
     * 
     * @return string
     */
    public function deleteAction()
    {
        // Batch removal
        if ($this->request->hasPost('toDelete')) {

            $ids = array_keys($this->request->getPost('toDelete'));
            $this->getPhotoManager()->deleteByIds($ids);

            $this->flashBag->set('success', 'Selected photos have been removed successfully');
        } else {
            $this->flashBag->set('warning', 'You should select at least one photo to remove');
        }

        // Single removal
        if ($this->request->hasPost('id')) {
            $id = $this->request->getPost('id');

            if ($this->getPhotoManager()->deleteById($id)) {
                $this->flashBag->set('success', 'Selected photo has been removed successfully');
                return '1';
            }
        }

        return '1';
    }

    /**
     * Persists a photo
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('photo');

        $formValidator = $this->validatorFactory->build(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'order' => new Pattern\Order()
                )
            ),
            'file' => array(
                'source' => $this->request->getFiles(),
                'definition' => array(
                    'file' => new Pattern\ImageFile(array(
                        'required' => !$input['id']
                    ))
                )
            )
        ));

        if ($formValidator->isValid()) {
            $photoManager = $this->getPhotoManager();

            if ($input['id']) {
                $this->flashBag->set('success', 'The photo has been updated successfully');
                return $this->getPhotoManager()->update($this->request->getAll()) ? '1' : '0';

            } else {
                $photoManager->add($this->request->getAll());

                $this->flashBag->set('success', 'A photo has been added successfully');
                return $photoManager->getLastId();
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
