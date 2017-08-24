<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Respinar\Photogallery',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Library
	'Respinar\Photogallery\ModulePhotogalleryList'   => 'system/modules/photogallery/library/Respinar/Photogallery/Frontend/Module/ModulePhotogalleryList.php',
	'Respinar\Photogallery\ModulePhotogalleryAlbum'  => 'system/modules/photogallery/library/Respinar/Photogallery/Frontend/Module/ModulePhotogalleryAlbum.php',
	'Respinar\Photogallery\ModulePhotogallery'       => 'system/modules/photogallery/library/Respinar/Photogallery/Frontend/Module/ModulePhotogallery.php',
	'Respinar\Photogallery\ContentPhotogallery'      => 'system/modules/photogallery/library/Respinar/Photogallery/Frontend/Element/ContentPhotogallery.php',
	'Respinar\Photogallery\ContentPhotogalleryAlbum' => 'system/modules/photogallery/library/Respinar/Photogallery/Frontend/Element/ContentPhotogalleryAlbum.php',
	'Respinar\Photogallery\Photogallery'             => 'system/modules/photogallery/library/Respinar/Photogallery/Photogallery.php',
	'Respinar\Photogallery\PhotogalleryAlbumModel'   => 'system/modules/photogallery/library/Respinar/Photogallery/Model/PhotogalleryAlbumModel.php',
	'Respinar\Photogallery\PhotogalleryModel'        => 'system/modules/photogallery/library/Respinar/Photogallery/Model/PhotogalleryModel.php',
));


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
