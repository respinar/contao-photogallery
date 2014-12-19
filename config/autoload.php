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
	'photogallery\ModulePhotogalleryList'    => 'system/modules/photogallery/modules/ModulePhotogalleryList.php',
	'photogallery\ModulePhotogalleryDetail'  => 'system/modules/photogallery/modules/ModulePhotogalleryDetail.php',

	// Models
	'photogallery\PhotogalleryCategoryModel' => 'system/modules/photogallery/models/PhotogalleryCategoryModel.php',
	'photogallery\PhotogalleryAlbumModel'    => 'system/modules/photogallery/models/PhotogalleryAlbumModel.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_photogallery_list'   => 'system/modules/photogallery/templates/modules',
	'mod_photogallery_detail' => 'system/modules/photogallery/templates/modules',
));
