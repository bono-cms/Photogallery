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
     * @param \Krystal\Stdlib\VirtualEntity|array $album
     * @param string $title
     * @return string
     */
    private function createForm($album, $title)
    {
        // Load view plugins
        $this->view->getPluginBag()->load($this->getWysiwygPluginName());

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

        // CMS configuration object
        $config = $this->getService('Cms', 'configManager')->getEntity();

        $album = new VirtualEntity();
        $album->setSeo(true)
              ->setChangeFreq($config->getSitemapFrequency())
              ->setPriority($config->getSitemapPriority());

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
        $album = $this->getAlbumManager()->fetchById($id, true);

        if ($album !== false) {
            $name = $this->getCurrentProperty($album, 'name');
            return $this->createForm($album, $this->translator->translate('Edit the album "%s"', $name));
        } else {
            return false;
        }
    }

    /**
     * Deletes an album with its content by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $album = $this->getAlbumManager()->fetchById($id, false);

        if ($album !== false) {
            $historyService = $this->getService('Cms', 'historyManager');
            $historyService->write('Photogallery', 'The album "%s" has been removed', $album->getName());

            $service = $this->getModuleService('albumManager');
            $service->deleteById($id);

            $this->flashBag->set('success', 'Selected element has been removed successfully');
            return '1';
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

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name()
                )
            )
        ));

        if (1) {
            // Current page name
            $name = $this->getCurrentProperty($this->request->getPost('translation'), 'name');

            $service = $this->getModuleService('albumManager');
            $historyService = $this->getService('Cms', 'historyManager');

            if (!empty($input['id'])) {
                if ($service->update($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been updated successfully');

                    $historyService->write('Photogallery', 'Album "%s" has been updated', $name);
                    return '1';
                }

            } else {
                if ($service->add($this->request->getAll())) {
                    $this->flashBag->set('success', 'The element has been created successfully');

                    $historyService->write('Photogallery', 'The album "%s" has been created', $name);
                    return $service->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
