<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'album_teaser'           => 'system/modules/photogallery/templates/album',
	'album_full'             => 'system/modules/photogallery/templates/album',
	'mod_photogallery_list'  => 'system/modules/photogallery/templates/modules',
	'mod_photogallery_album' => 'system/modules/photogallery/templates/modules',
	'photogallery_image'     => 'system/modules/photogallery/templates/image',
	'ce_photogallery_album'  => 'system/modules/photogallery/templates/elements',
));
