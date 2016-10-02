<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Photogallery
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'photogallery',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'photogallery\Photogallery'              => 'system/modules/photogallery/classes/Photogallery.php',

	// Modules
	'photogallery\ModulePhotogallery'        => 'system/modules/photogallery/modules/ModulePhotogallery.php',
	'photogallery\ModulePhotogalleryList'    => 'system/modules/photogallery/modules/ModulePhotogalleryList.php',
	'photogallery\ModulePhotogalleryAlbum'  => 'system/modules/photogallery/modules/ModulePhotogalleryAlbum.php',	

	// Models
	'photogallery\PhotogalleryModel' => 'system/modules/photogallery/models/PhotogalleryModel.php',
	'photogallery\PhotogalleryAlbumModel'    => 'system/modules/photogallery/models/PhotogalleryAlbumModel.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_photogallery_list'   => 'system/modules/photogallery/templates/modules',
	'mod_photogallery_album'  => 'system/modules/photogallery/templates/modules',
	'album_full'              => 'system/modules/photogallery/templates/album',
	'album_teaser'            => 'system/modules/photogallery/templates/album',
));
