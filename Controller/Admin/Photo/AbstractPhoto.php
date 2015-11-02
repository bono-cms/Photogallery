<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Photogallery\Controller\Admin\Photo;

use Cms\Controller\Admin\AbstractController;
use Krystal\Tree\AdjacencyList\TreeBuilder;
use Krystal\Tree\AdjacencyList\Render\PhpArray;
use Krystal\Validate\Pattern;

abstract class AbstractPhoto extends AbstractController
{
    /**
     * Returns prepared and configured form validator
     * 
     * @param array $post
     * @param array $files
     * @param $edit Whether is meant for edit form
     * @return \Krystal\Validate\ValidatorChain
     */
    final protected function getValidator(array $post, array $files, $edit = false)
    {
        return $this->validatorFactory->build(array(
            'input' => array(
                'source' => $post,
                'definition' => array(
                    'order' => new Pattern\Order()
                )
            ),
            'file' => array(
                'source' => $files,
                'definition' => array(
                    'file' => new Pattern\ImageFile(array(
                        'required' => !$edit
                    ))
                )
            )
        ));
    }

    /**
     * Returns albums tree
     * 
     * @return array
     */
    final protected function getALbumsTree()
    {
        $treeBuilder = new TreeBuilder($this->getModuleService('albumManager')->fetchAll());
        return $treeBuilder->render(new PhpArray('name'));
    }

    /**
     * Loads breadcrumbs
     * 
     * @param string $title
     * @return void
     */
    final protected function loadBreadcrumbs($title)
    {
        $this->view->getBreadcrumbBag()->addOne('Photogallery', 'Photogallery:Admin:Browser@indexAction')
                                       ->addOne($title);
    }

    /**
     * Returns photo manager
     * 
     * @return \Photogallery\Service\PhotoManager
     */
    final protected function getPhotoManager()
    {
        return $this->getModuleService('photoManager');
    }

    /**
     * Loads shared plugins
     * 
     * @return void
     */
    final protected function loadSharedPlugins()
    {
        $this->view->getPluginBag()
                   ->appendScript('@Photogallery/admin/photo.form.js');
    }

    /**
     * Returns shared template path
     * 
     * @return string
     */
    final protected function getTemplatePath()
    {
        return 'photo.form';
    }
}
