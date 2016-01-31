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

use Krystal\Validate\Pattern;
use Krystal\Stdlib\VirtualEntity;
use Cms\Controller\Admin\AbstractController;

final class Album extends AbstractController
{
    /**
     * Returns album tree with an empty prompt
     * 
     * @return array
     */
    private function getAlbumsTree()
    {
        $text = sprintf('— %s —', $this->translator->translate('None'));
        return $this->getAlbumManager()->getPromtWithAlbumsTree($text);
    }

    /**
     * Returns album manager service
     * 
     * @return \Photogallery\Service\AlbumManager
     */
    private function getAlbumManager()
    {
        return $this->getModuleService('albumManager');
    }

    /**
     * Creates album form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $album
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $album, $title)
    {
        // Load view plugins
        $this->loadMenuWidget();
        $this->view->getPluginBag()->appendScript('@Photogallery/admin/album.form.js')
                                   ->load($this->getWysiwygPluginName());

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Photogallery', 'Photogallery:Admin:Browser@indexAction')
                                       ->addOne($title);

        return $this->view->render('album.form', array(
            'albums' => $this->getAlbumsTree(),
            'album' => $album
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

        $album = new VirtualEntity();
        $album->setSeo(true);

        return $this->createForm($album, 'Add an album');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $album = $this->getAlbumManager()->fetchById($id);

        if ($album !== false) {
            return $this->createForm($album, 'Edit the album');
        } else {
            return false;
        }
    }

    /**
     * Deletes an album with its content by its associated id
     * 
     * @return string
     */
    public function deleteAction()
    {
        if ($this->request->hasPost('id')) {
            $id = $this->request->getPost('id');
            // Grab a service
            $albumManager = $this->getAlbumManager();

            if ($albumManager->deleteById($id)) {
                $this->flashBag->set('success', 'Selected album has been removed successfully');
                return '1';
            }
        }
    }

    /**
     * Persists an album
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('album');

        $formValidator = $this->validatorFactory->build(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name()
                )
            )
        ));

        if ($formValidator->isValid()) {
            $albumManager = $this->getAlbumManager();

            if ($input['id']) {
                $albumManager->update($this->request->getAll());
                $this->flashBag->set('success', 'The album has been updated successfully');
                return '1';

            } else {
                $albumManager->add($this->request->getAll());

                $this->flashBag->set('success', 'An album has been created successfully');
                return $albumManager->getLastId();
            }
        } else {
            return $formValidator->getErrors();
        }
    }
}
