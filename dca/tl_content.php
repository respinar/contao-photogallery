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


$GLOBALS['TL_DCA']['tl_content']['palettes']['photogallery_album'] = '{title_legend},type,headline;
                                                                      {album_legend},photogallery_album;
                                                                      {template_legend},photogallery_metaFields,photogallery_template,customTpl;
                                                                      {image_legend},size,photogallery_itemClass;
                                                                      {protected_legend:hide},protected;
                                                                      {expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['photogallery_album'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_content']['photogallery_album'],
	'exclude'              => true,
	'inputType'            => 'select',
	'foreignKey'           => 'tl_photogallery_album.title',
	'eval'                 => array('helpwizard'=>true,'chosen'=>true,'multiple'=>false, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['photogallery_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_content']['photogallery_album_template'],
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array('tl_content_photogallery', 'getAlbumTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['photogallery_metaFields'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['photogallery_metaFields'],
	'default'                 => array('date'),
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('date','location','photographer'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_content']['fields']['photogallery_itemClass'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['photogallery_itemClass'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'clr w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

/**
 * Class tl_content_photogallery
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Hamid Abbaszadeh 2014
 * @author     Hamid Abbaszadeh <http://respinar.com>
 * @package    Catalog
 */
class tl_content_photogallery extends Backend
{

	/**
	 * Return all prices templates as array
	 *
	 * @return array
	 */
	public function getAlbumTemplates()
	{
		return $this->getTemplateGroup('album_');
	}
}
