<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2019 Leo Feyer
 *
 * @package   photogallery
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL3
 * @copyright respinar 2014-2019
 */


$GLOBALS['TL_DCA']['tl_module']['palettes']['photogallery_list'] =   '{title_legend},name,headline,type;
                                                                      {catalog_legend},photogalleries;
                                                                      {config_legend},photogallery_featured,photogallery_detailModule,numberOfItems,perPage,skipFirst;
                                                                      {template_legend},photogallery_metaFields,photogallery_template,customTpl;
                                                                      {album_legend},imgSize,photogallery_listClass,photogallery_itemClass;
                                                                      {protected_legend:hide},protected;
                                                                      {expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['photogallery_album'] = '{title_legend},name,headline,type;
                                                                      {catalog_legend},photogalleries;
                                                                      {template_legend},photogallery_metaFields,photogallery_template,customTpl;
                                                                      {image_legend},imgSize,fullsize,photogallery_listClass,photogallery_itemClass,photogallery_sortBy;
                                                                      {protected_legend:hide},protected;
                                                                      {expert_legend:hide},guests,cssID,space';

/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['photogalleries'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['photogalleries'],
	'exclude'              => true,
	'inputType'            => 'checkbox',
	'foreignKey'           => 'tl_photogallery.title',
	'eval'                 => array('multiple'=>true, 'mandatory'=>true),
    'sql'                  => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['photogallery_template'] = array
(
	'label'                => &$GLOBALS['TL_LANG']['tl_module']['photogallery_template'],
	'exclude'              => true,
	'inputType'            => 'select',
	'options_callback'     => array('tl_module_photogallery', 'getAlbumTemplates'),
	'eval'                 => array('tl_class'=>'w50'),
    'sql'                  => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['photogallery_featured'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['photogallery_featured'],
	'default'                 => 'all',
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('all_album', 'featured_album', 'unfeatured_album'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(20) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['photogallery_metaFields'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['photogallery_metaFields'],
	'default'                 => array('date'),
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('date','location','photographer'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['photogallery_detailModule'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['photogallery_detailModule'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_photogallery', 'getDetailModules'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['photogallery_itemClass'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['photogallery_itemClass'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['photogallery_listClass'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['photogallery_listClass'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>128, 'tl_class'=>'clr w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['photogallery_sortBy'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['photogallery_sortBy'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('custom', 'name_asc', 'name_desc', 'date_asc', 'date_desc', 'random'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "varchar(32) NOT NULL default ''"
);

/**
 * Class tl_module_photogallery
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Hamid Abbaszadeh 2014
 * @author     Hamid Abbaszadeh <http://respinar.com>
 * @package    Catalog
 */
class tl_module_photogallery extends Backend
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

	/**
	 * Get all product detail modules and return them as array
	 * @return array
	 */
	public function getDetailModules()
	{
		$arrModules = array();
		$objModules = $this->Database->execute("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE m.type='photogallery_album' ORDER BY t.name, m.name");

		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}
}
