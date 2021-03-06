
/* Albums */
DROP TABLE IF EXISTS `bono_module_photoalbum_albums`;
CREATE TABLE `bono_module_photoalbum_albums` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `parent_id` INT NOT NULL COMMENT 'Parent album id in current table',
    `order` INT NOT NULL COMMENT 'Sort order',
    `seo` varchar(1) NOT NULL,
    `cover` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_photoalbum_albums_translations`;
CREATE TABLE `bono_module_photoalbum_albums_translations` (
    `id` INT NOT NULL,
    `lang_id` INT NOT NULL COMMENT 'Language identificator',
    `web_page_id` INT NOT NULL COMMENT 'Album web page id',
    `title` varchar(255) NOT NULL,
    `name`  varchar(255) NOT NULL,
    `description` LONGTEXT NOT NULL COMMENT 'Album description that comes from WYSIWYG',
    `keywords` TEXT NOT NULL COMMENT 'Keywords for SEO',
    `meta_description` TEXT NOT NULL COMMENT 'Meta description for SEO',

    FOREIGN KEY (id) REFERENCES bono_module_photoalbum_albums(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE,
    FOREIGN KEY (web_page_id) REFERENCES bono_module_cms_webpages(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

/* Photos */
DROP TABLE IF EXISTS `bono_module_photoalbum_photos`;
CREATE TABLE `bono_module_photoalbum_photos` (
	`id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`album_id` INT NOT NULL,
	`photo` varchar(254) NOT NULL,
	`order` INT NOT NULL COMMENT 'Sort order',
	`published` varchar(1) NOT NULL,
	`date` INT NOT NULL COMMENT 'Timestamp of uploading',

    FOREIGN KEY (album_id) REFERENCES bono_module_photoalbum_albums(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_photoalbum_photos_translations`;
CREATE TABLE `bono_module_photoalbum_photos_translations` (
    `id` INT NOT NULL,
    `lang_id` INT NOT NULL,
    `name` varchar(254) NOT NULL,
    `description` TEXT NOT NULL,

    FOREIGN KEY (id) REFERENCES bono_module_photoalbum_photos(id) ON DELETE CASCADE,
    FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = UTF8;
