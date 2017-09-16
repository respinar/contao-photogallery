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
namespace Respinar\Photogallery;


/**
 * Class ModulePhotogalleryDetail
 *
 * @copyright  respinar 2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ContentPhotogalleryAlbum extends \ContentPhotogallery
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_photogallery_album';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['photogallery_album'][0]) . ' ###';

			$objAlbum = \PhotogalleryAlbumModel::findBy('id',$this->photogallery_album);

			$objTemplate->title = $this->headline;
			$objTemplate->id = $objAlbum->id;
			$objTemplate->link = $objAlbum->title;
			$objTemplate->href = 'contao/main.php?do=photogallery&amp;table=tl_photogallery_album&amp;act=edit&amp;id=' . $objAlbum->id;

			$objFile = \FilesModel::findByUuid($objAlbum->singleSRC);
			
			$objTemplate->singleSRC = $objFile->path;

			return $objTemplate->parse();
		}

		// Set the item from the auto_item parameter
		if (!isset($_GET['items']) && $GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))
		{
			\Input::setGet('items', \Input::get('auto_item'));
		}

		//$this->photogalleries = $this->sortOutProtected(deserialize($this->photogalleries));

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		global $objPage;

		$objAlbum = \PhotogalleryAlbumModel::findBy('id',$this->photogallery_album);

		$this->Template->album = $this->parseAlbumFull($objAlbum);

	}
}
