<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] = str_replace('formp;', 'formp;{photogallery_legend},photogallerys,photogalleryp;', $GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']);


/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['photogallerys'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['photogallerys'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_photogallery.title',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_user_group']['fields']['photogalleryp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['photogalleryp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('create', 'delete'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL"
);
