<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   photogallery
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL3
 * @copyright respinar 2014
 */

/**
 * Back end modules
 */
array_insert($GLOBALS['BE_MOD']['content'], 1, array
(
	'photogallery' => array
	(
		'tables'     => array('tl_photogallery','tl_photogallery_album'),
		'icon'       => 'system/modules/photogallery/assets/icon.png',
	)
));

/**
 * Front end modules
 */

array_insert($GLOBALS['FE_MOD'], 2, array
(
	'photogallery' => array
	(
		'photogallery_list'   => 'Respinar\Photogallery\ModulePhotogalleryList',
		'photogallery_album'  => 'Respinar\Photogallery\ModulePhotogalleryAlbum'
	)
));

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['miscellaneous']['photogallery_album']   = 'Respinar\Photogallery\ContentPhotogalleryAlbum';


$GLOBALS['TL_HOOKS']['getSearchablePages'][] = array('Respinar\Photogallery\Photogallery', 'getSearchablePages');

// Registrieren im Hooks replaceInsertTags
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Respinar\Photogallery\Photogallery', 'albumURLInsertTags');


/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'photogallerys';
$GLOBALS['TL_PERMISSIONS'][] = 'photogalleryp';
