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
 * Class ModulePhotogalleryList
 *
 * @copyright  respinar 2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class ModulePhotogalleryList extends \ModulePhotogallery
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_photogallery_list';

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

		$this->photogalleries = $this->sortOutProtected(deserialize($this->photogalleries));

		// No photogallery categries available
		if (!is_array($this->photogalleries) || empty($this->photogalleries))
		{
			return '';
		}

		// Show the catalog detail if an item has been selected
		if ($this->photogallery_detailModule > 0 && (isset($_GET['items']) || ($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))))
		{
			return $this->getFrontendModule($this->photogallery_detailModule, $this->strColumn);
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{

		$offset = intval($this->skipFirst);
		$limit = null;

		// Maximum number of items
		if ($this->numberOfItems > 0)
		{
			$limit = $this->numberOfItems;
		}

		// Handle featured news
		if ($this->photogallery_featured == 'featured')
		{
			$blnFeatured = true;
		}
		elseif ($this->photogallery_featured == 'unfeatured')
		{
			$blnFeatured = false;
		}
		else
		{
			$blnFeatured = null;
		}

		$this->Template->albums = array();
		$this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyPhotogallery'];

		$intTotal = \PhotogalleryAlbumModel::countPublishedByPids($this->photogalleries);

		if ($intTotal < 1)
		{
			return;
		}

		$total = $intTotal - $offset;


		// Split the results
		if ($this->perPage > 0 && (!isset($limit) || $this->numberOfItems > $this->perPage))
		{
			// Adjust the overall limit
			if (isset($limit))
			{
				$total = min($limit, $total);
			}

			// Get the current page
			$id = 'page_n' . $this->id;
			$page = \Input::get($id) ?: 1;

			// Do not index or cache the page if the page number is outside the range
			if ($page < 1 || $page > max(ceil($total/$this->perPage), 1))
			{
				global $objPage;
				$objPage->noSearch = 1;
				$objPage->cache = 0;

				// Send a 404 header
				header('HTTP/1.1 404 Not Found');
				return;
			}

			// Set limit and offset
			$limit = $this->perPage;
			$offset += (max($page, 1) - 1) * $this->perPage;
			$skip = intval($this->skipFirst);

			// Overall limit
			if ($offset + $limit > $total + $skip)
			{
				$limit = $total + $skip - $offset;
			}

			// Add the pagination menu
			$objPagination = new \Pagination($total, $this->perPage, \Config::get('maxPaginationLinks'), $id);
			$this->Template->pagination = $objPagination->generate("\n  ");
		}

		// Get the items
		if (isset($limit))
		{
			$objAlbums = \PhotogalleryAlbumModel::findPublishedByPids($this->photogalleries, $blnFeatured, $limit, $offset);
		}
		else
		{
			$objAlbums = \PhotogalleryAlbumModel::findPublishedByPids($this->photogalleries, $blnFeatured, 0, $offset);
		}


		// Add the albums
		if ($objAlbums !== null)
		{
			$this->Template->albums = $this->parseAlbums($objAlbums);
		}

		$this->Template->gategories = $this->photogalleries;

	}
}
