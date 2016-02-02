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
        return $this->invokeRemoval('photoManager');
    }

    /**
     * Persists a photo
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('photo');

        return $this->invokeSave('photoManager', $input['id'], $this->request->getAll(), array(
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
    }
}
