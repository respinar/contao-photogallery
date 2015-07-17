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
 * Namespace
 */
namespace photogallery;


/**
 * Class ModulePhotogalleryDetail
 *
 * @copyright  respinar 2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ModulePhotogalleryDetail extends \ModulePhotogallery
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_photogallery_detail';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['photogallery_detail'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			\Input::setGet('items', \Input::get('auto_item'));
		}

		$this->photogalleries = $this->sortOutProtected(deserialize($this->photogalleries));

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		global $objPage;

		$this->Template->albums = '';
		$this->Template->referer = 'javascript:history.go(-1)';
		$this->Template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];

		$objAlbum = \PhotogalleryAlbumModel::findPublishedByParentAndIdOrAlias(\Input::get('items'),$this->photogalleries);

		// Overwrite the page title
		if ($objAlbum->title != '')
		{
			$objPage->pageTitle = strip_tags(strip_insert_tags($objAlbum->title));
		}

		// Overwrite the page description
		if ($objProduct->description != '')
		{
			$objPage->description = $this->prepareMetaDescription($objAlbum->description);
		}

		$arrAlbum = $this->parseAlbumFull($objAlbum);

		$this->Template->albums = $arrAlbum;

	}
}
