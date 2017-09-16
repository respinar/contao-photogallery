<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

/**
 * Register PSR-0 namespaces
 */
 if (class_exists('NamespaceClassLoader')) {
    NamespaceClassLoader::add('Respinar\Photogallery', 'system/modules/photogallery/src');
}

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'album_teaser'           => 'system/modules/photogallery/templates/album',
	'album_full'             => 'system/modules/photogallery/templates/album',
	'mod_photogallery_list'  => 'system/modules/photogallery/templates/modules',
	'mod_photogallery_album' => 'system/modules/photogallery/templates/modules',
	'ce_photogallery_album'  => 'system/modules/photogallery/templates/elements',
	'photogallery_image'     => 'system/modules/photogallery/templates/image',
));
