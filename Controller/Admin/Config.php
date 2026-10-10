<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Photogallery\Controller\Admin;

use Krystal\Validation\Validator;
use Cms\Controller\Admin\AbstractConfigController;

final class Config extends AbstractConfigController
{
    /**
     * {@inheritDoc}
     */
    protected function configureValidator(Validator $validator)
    {
        $validator->field('config.max_img_height', 'Max image height')
                  ->required()
                  ->addRule('numeric');

        $validator->field('config.max_img_width', 'Max image width')
                  ->required()
                  ->addRule('numeric');

        $validator->field('config.photos_per_page', 'Photos per page')
                  ->required()
                  ->addRule('numeric')
                  ->addRule('greaterthan', null, ['min' => 0]);

        $validator->field('config.quality', 'Quality')
                  ->required()
                  ->addRule('numeric')
                  ->addRule('between', null, ['min' => 1, 'max' => 100]);

        $validator->field('config.thumb_height', 'Thumb height')
                  ->required()
                  ->addRule('numeric');

        $validator->field('config.thumb_width', 'Thumb width')
                  ->required()
                  ->addRule('numeric');
    }
}