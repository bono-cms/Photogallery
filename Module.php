<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Photogallery;

use Cms\AbstractCmsModule;
use Krystal\Image\Tool\ImageManager;
use Krystal\Stdlib\VirtualEntity;
use Photogallery\Service\AlbumManager;
use Photogallery\Service\PhotoManager;
use Photogallery\Service\TaskManager;
use Photogallery\Service\SiteService;

final class Module extends AbstractCmsModule
{
    /**
     * Returns album image manager
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @return \Krystal\Image\ImageManager
     */
    private function getAlbumImageManager(VirtualEntity $config)
    {
        $plugins = array(
            'thumb' => array(
                'dimensions' => array(
                    // Dimensions for administration panel
                    array(200, 200),
                    // Dimensions for the site
                    array($config->getAlbumThumbWidth(), $config->getAlbumThumbHeight())
                )
            ),

            'original' => array(
                'prefix' => 'original'
            )
        );

        return new ImageManager(
            '/data/uploads/module/photogallery/albums',
            $this->appConfig->getRootDir(),
            $this->appConfig->getRootUrl(),
            $plugins
        );
    }

    /**
     * Returns prepared image manager
     * 
     * @param \Krystal\Stdlib\VirtualEntity $config
     * @return \Krystal\Image\Too\ImageManager
     */
    private function getImageManagerService(VirtualEntity $config)
    {
        $plugins = array(
            'thumb' => array(
                'quality' => $config->getQuality(),
                'dimensions' => array(
                    // Dimensions for administration panel
                    array(400, 200),
                    // Dimensions for site previews. 200 are default values
                    array($config->getWidth(), $config->getHeight())
                )
            ),

            'original' => array(
                'quality' => $config->getQuality(),
                'prefix' => 'original',
                'max_width' => $config->getMaxWidth(),
                'max_height' => $config->getMaxHeight(),
            )
        );

        return new ImageManager(
            '/data/uploads/module/photogallery/photos',
            $this->appConfig->getRootDir(),
            $this->appConfig->getRootUrl(),
            $plugins
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceProviders()
    {
        $config = $this->createConfigService();

        // Build mappers
        $albumMapper = $this->getMapper('/Photogallery/Storage/MySQL/AlbumMapper');
        $photoMapper = $this->getMapper('/Photogallery/Storage/MySQL/PhotoMapper');

        // Grab required services
        $historyManager = $this->getHistoryManager();
        $webPageManager = $this->getWebPageManager();
        $imageManager = $this->getImageManagerService($config->getEntity());

        $albumManager = new AlbumManager(
            $albumMapper, 
            $photoMapper, 
            $this->getAlbumImageManager($config->getEntity()), 
            $imageManager, 
            $webPageManager, 
            $historyManager
        );

        $photoManager = new PhotoManager($photoMapper, $albumMapper, $imageManager, $historyManager);

        return array(
            'siteService' => new SiteService($photoManager, $albumManager),
            'configManager' => $config,
            'taskManager' => new TaskManager($photoMapper, $albumManager),
            'photoManager' => $photoManager,
            'albumManager' => $albumManager
        );
    }
}
