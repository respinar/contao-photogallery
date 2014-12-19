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
abstract class ModulePhotogallery extends \Module
{


	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = array();


	/**
	 * Sort out protected archives
	 * @param array
	 * @return array
	 */
	protected function sortOutProtected($arrCategories)
	{
		if (BE_USER_LOGGED_IN || !is_array($arrCategories) || empty($arrCategories))
		{
			return $arrCategories;
		}

		$this->import('FrontendUser', 'User');
		$objCategory = \CatalogCategoryModel::findMultipleByIds($arrCategories);
		$arrCategories = array();

		if ($objCategory !== null)
		{
			while ($objCategory->next())
			{
				if ($objCategory->protected)
				{
					if (!FE_USER_LOGGED_IN)
					{
						continue;
					}

					$groups = deserialize($objCategory->groups);

					if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
					{
						continue;
					}
				}

				$arrCategories[] = $objCategory->id;
			}
		}

		return $arrCategories;
	}


	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parseAlbum($objAlbum, $blnAddCategory=false, $strClass='', $intCount=0)
	{
		global $objPage;

		$objTemplate = new \FrontendTemplate($this->album_template);
		$objTemplate->setData($objAlbum->row());

		$objTemplate->class = (($this->itemClass != '') ? ' ' . $this->itemClass : '') . $strClass;


		$objTemplate->title       = $objAlbum->title;
		$objTemplate->alias       = $objAlbum->alias;

		$objTemplate->description = $objAlbum->description;
		$objTemplate->keywords    = $objAlbum->keywords;

		$objTemplate->href        = $this->generateAlbumUrl($objAlbum, $blnAddCategory);
		$objTemplate->more        = $this->generateLink($GLOBALS['TL_LANG']['MSC']['moredetail'], $objAlbum, $blnAddCategory, true);

		$arrMeta = $this->getMetaFields($objAlbum);

		$objTemplate->category    = $objAlbum->getRelated('pid');

		$objTemplate->count = $intCount; // see #5708

		$arrMeta = $this->getMetaFields($objAlbum);

		// Add the meta information
		$objTemplate->date = $arrMeta['date'];
		$objTemplate->hasMetaFields = !empty($arrMeta);
		$objTemplate->timestamp = $objAlbum->date;
		$objTemplate->datetime = date('Y-m-d\TH:i:sP', $objAlbum->date);

		$objTemplate->addImage = false;

		// Add an image
		if ($objAlbum->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objAlbum->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objAlbum->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrAlbum = $objAlbum->row();

				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = deserialize($this->imgSize);

					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrAlbum['size'] = $this->imgSize;
					}
				}

				$arrAlbum['singleSRC'] = $objModel->path;
				$this->addImageToTemplate($objTemplate, $arrAlbum);
			}
		}

		return $objTemplate->parse();
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseAlbums($objAlbums, $blnAddCategory=false)
	{
		$limit = $objAlbums->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrAlbums = array();

		while ($objAlbums->next())
		{
			$arrAlbums[] = $this->parseAlbum($objAlbums, $blnAddCategory, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

		return $arrAlbums;
	}

	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateAlbumUrl($objItem, $blnAddCategory=false)
	{
		$strCacheKey = 'id_' . $objItem->id;

		// Load the URL from cache
		if (isset(self::$arrUrlCache[$strCacheKey]))
		{
			return self::$arrUrlCache[$strCacheKey];
		}

		// Initialize the cache
		self::$arrUrlCache[$strCacheKey] = null;

		// Link to the default page
		if (self::$arrUrlCache[$strCacheKey] === null)
		{
			$objPage = \PageModel::findByPk($objItem->getRelated('pid')->jumpTo);

			if ($objPage === null)
			{
				self::$arrUrlCache[$strCacheKey] = ampersand(\Environment::get('request'), true);
			}
			else
			{
				self::$arrUrlCache[$strCacheKey] = ampersand($this->generateFrontendUrl($objPage->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $objItem->alias != '') ? $objItem->alias : $objItem->id)));
			}

		}

		return self::$arrUrlCache[$strCacheKey];
	}


	/**
	 * Generate a link and return it as string
	 * @param string
	 * @param object
	 * @param boolean
	 * @param boolean
	 * @return string
	 */
	protected function generateLink($strLink, $objAlbum, $blnAddCategory=false, $blnIsReadMore=false)
	{

		return sprintf('<a href="%s" title="%s">%s%s</a>',
						$this->generateAlbumUrl($objAlbum, $blnAddCategory),
						specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objAlbum->title), true),
						$strLink,
						($blnIsReadMore ? ' <span class="invisible">'.$objAlbum->title.'</span>' : ''));

	}

	/**
	 * Return the meta fields of a news article as array
	 * @param object
	 * @return array
	 */
	protected function getMetaFields($objAlbum)
	{
		$meta = deserialize($this->photogallery_metaFields);

		if (!is_array($meta))
		{
			return array();
		}

		global $objPage;
		$return = array();

		foreach ($meta as $field)
		{
			switch ($field)
			{
				case 'date':
					$return['date'] = \Date::parse($objPage->datimFormat, $objProduct->date);
					break;
				case 'location':
					$return['location'] = $objProduct->location;
					break;
				case 'photographer':
					$return['photographer'] = $objProduct->photographer;
					break;
			}
		}

		return $return;
	}

}
