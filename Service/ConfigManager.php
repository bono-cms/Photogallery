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

use Krystal\Config\ConfigModuleService;
use Krystal\Stdlib\VirtualEntity;

final class ConfigManager extends ConfigModuleService
{
    /**
     * {@inheritDoc}
     */
    public function getEntity()
    {
        $entity = new VirtualEntity();
        $entity->setPerPageCount($this->get('photos_per_page', 5), VirtualEntity::FILTER_INT)
               ->setWidth($this->get('thumb_width', 200), VirtualEntity::FILTER_FLOAT)
               ->setHeight($this->get('thumb_height', 200), VirtualEntity::FILTER_FLOAT)
               ->setMaxHeight($this->get('max_img_height', 200), VirtualEntity::FILTER_FLOAT)
               ->setMaxWidth($this->get('max_img_width', 200), VirtualEntity::FILTER_FLOAT)
               ->setQuality($this->get('quality', 75), VirtualEntity::FILTER_FLOAT)
               ->setLanguageSupport($this->get('language_support'), VirtualEntity::FILTER_BOOL)
               ->setAlbumThumbWidth($this->get('album_thumb_width', 200), VirtualEntity::FILTER_FLOAT)
               ->setAlbumThumbHeight($this->get('album_thumb_height', 200), VirtualEntity::FILTER_FLOAT)
               ->setLanguageSupportOptions(array(
                    '0' => 'One photogallery version for all languages',
                    '1' => 'Each language must have its own photogallery version',
                ));

        return $entity;
    }
}
