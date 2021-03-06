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
     * Renders batch upload form
     * 
     * @return string
     */
    public function batchFormAction()
    {
        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Photogallery', 'Photogallery:Admin:Browser@indexAction')
                                       ->addOne('Batch photo uploading');

        return $this->view->render('batch.form', array(
            'albums' => $this->getModuleService('albumManager')->getAlbumsTree(false)
        ));
    }

    /**
     * Performs batch uploading
     * 
     * @return int
     */
    public function batchUploadAction()
    {
        // Grab parameters
        $request = $this->request->getAll();
        $activeLanguageIds = $this->getService('Cms', 'languageManager')->fetchActiveIds();

        // Now do batch uploading
        $this->getModuleService('photoManager')->batch($request, $activeLanguageIds);

        // And finally, inform about success
        $this->flashBag->set('success', 'Photos have been uploaded successfully');
        return 1;
    }

    /**
     * Creates a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity|array $photo
     * @param string $title
     * @return string
     */
    private function createForm($photo, $title)
    {
        // Load view plugins
        $this->view->getPluginBag()
                   ->appendScript('@Photogallery/admin/photo.form.js');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Photogallery', 'Photogallery:Admin:Browser@indexAction')
                                       ->addOne($title);

        return $this->view->render('photo.form', array(
            'albums' => $this->getModuleService('albumManager')->getAlbumsTree(false),
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
        $photo = $this->getModuleService('photoManager')->fetchById($id);

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
            if ($this->getModuleService('photoManager')->updateSettings($this->request->getPost())) {
                $this->flashBag->set('success', 'Settings have been updated successfully');
                return '1';
            }
        }
    }

    /**
     * Deletes a photo by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('photoManager');
        $historyService = $this->getService('Cms', 'historyManager');

        // Batch removal
        if ($this->request->hasPost('batch')) {
            $ids = array_keys($this->request->getPost('batch'));

            $service->deleteByIds($ids);
            $this->flashBag->set('success', 'Selected elements have been removed successfully');

            // Save in the history
            $historyService->write('Photogallery', 'Batch removal of %s photos', count($ids));

        } else {
            $this->flashBag->set('warning', 'You should select at least one element to remove');
        }

        // Single removal
        if (!empty($id)) {
            $photo = $this->getModuleService('photoManager')->fetchById($id, false);

            $service->deleteById($id);
            $this->flashBag->set('success', 'Selected element has been removed successfully');

            // Save in the history
            $historyService->write('Photogallery', 'The photo "%s" has been removed', $photo->getName());
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

        $formValidator = $this->createValidator(array(
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

        if (1) {
            // Current page name
            $name = $this->getCurrentProperty($this->request->getPost('translation'), 'name');

            $service = $this->getModuleService('photoManager');
            $historyService = $this->getService('Cms', 'historyManager');

            if (!empty($input['id'])) {
                if ($service->update($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been updated successfully');

                    $historyService->write('Photogallery', 'The photo "%s" has been updated', $name);
                    return '1';
                }

            } else {
                if ($service->add($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been created successfully');

                    $historyService->write('Photogallery', 'The photo "%s" has been updated', $name);
                    return $service->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
