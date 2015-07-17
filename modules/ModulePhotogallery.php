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
	 * Files object
	 * @var \FilesModel
	 */
	protected $objFiles;


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
		$objCategory = \PhotogalleryModel::findMultipleByIds($arrCategories);
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

		$objTemplate->class = (($this->item_Class != '') ? ' ' . $this->item_Class : '') . $strClass;

		$objTemplate->href        = $this->generateAlbumUrl($objAlbum, $blnAddCategory);
		$objTemplate->more        = $this->generateLink($GLOBALS['TL_LANG']['MSC']['moredetail'], $objAlbum, $blnAddCategory, true);

		$arrMeta = $this->getMetaFields($objAlbum);

		$objTemplate->gallery    = $objAlbum->getRelated('pid');

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
			$this->addImageToTemplate($objTemplate, $this->parseImage($objAlbum,$this->imgSize));
		}

		return $objTemplate->parse();
	}


	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parseAlbumFull($objAlbum, $blnAddCategory=false, $strClass='', $intCount=0)
	{
		global $objPage;

		$objTemplate = new \FrontendTemplate($this->album_template);
		$objTemplate->setData($objAlbum->row());

		$objTemplate->class = (($this->item_Class != '') ? ' ' . $this->item_Class : '') . $strClass;
		$objTemplate->itemClass = $this->item_Class;


		$objTemplate->title       = $objAlbum->title;
		$objTemplate->alias       = $objAlbum->alias;

		$objTemplate->description = $objAlbum->description;
		$objTemplate->keywords    = $objAlbum->keywords;
		$objTemplate->teaser      = $objAlbum->teaser;

		$objTemplate->href        = $this->generateAlbumUrl($objAlbum, $blnAddCategory);
		$objTemplate->more        = $this->generateLink($GLOBALS['TL_LANG']['MSC']['moredetail'], $objAlbum, $blnAddCategory, true);

		$arrMeta = $this->getMetaFields($objAlbum);

		$objTemplate->gallery    = $objAlbum->getRelated('pid');

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
			$this->addImageToTemplate($objTemplate, $this->parseImage($objAlbum,$this->imgSize));
		}

		$this->multiSRC = deserialize($objAlbum->multiSRC);

		// Get the file entries from the database
		$this->objFiles = \FilesModel::findMultipleByUuids($this->multiSRC);

		$objFiles = $this->objFiles;

		// Get all images
		while ($objFiles->next())
		{
			// Continue if the files has been processed or does not exist
			if (isset($images[$objFiles->path]) || !file_exists(TL_ROOT . '/' . $objFiles->path))
			{
				continue;
			}

			// Single files
			if ($objFiles->type == 'file')
			{
				$objFile = new \File($objFiles->path, true);

				if (!$objFile->isImage)
				{
					continue;
				}

				$arrMeta = $this->getMetaData($objFiles->meta, $objPage->language);

				if (empty($arrMeta))
				{
					if ($this->metaIgnore)
					{
						continue;
					}
					elseif ($objPage->rootFallbackLanguage !== null)
					{
						$arrMeta = $this->getMetaData($objFiles->meta, $objPage->rootFallbackLanguage);
					}
				}

				// Use the file name as title if none is given
				if ($arrMeta['title'] == '')
				{
					$arrMeta['title'] = specialchars($objFile->basename);
				}

				// Add the image
				$images[$objFiles->path] = array
				(
					'id'        => $objFiles->id,
					'uuid'      => $objFiles->uuid,
					'name'      => $objFile->basename,
					'singleSRC' => $objFiles->path,
					'alt'       => $arrMeta['title'],
					'imageUrl'  => $arrMeta['link'],
					'caption'   => $arrMeta['caption']
				);

				$auxDate[] = $objFile->mtime;
			}

			// Folders
			else
			{
				$objSubfiles = \FilesModel::findByPid($objFiles->uuid);

				if ($objSubfiles === null)
				{
					continue;
				}

				while ($objSubfiles->next())
				{
					// Skip subfolders
					if ($objSubfiles->type == 'folder')
					{
						continue;
					}

					$objFile = new \File($objSubfiles->path, true);

					if (!$objFile->isImage)
					{
						continue;
					}

					$arrMeta = $this->getMetaData($objSubfiles->meta, $objPage->language);

					if (empty($arrMeta))
					{
						if ($this->metaIgnore)
						{
							continue;
						}
						elseif ($objPage->rootFallbackLanguage !== null)
						{
							$arrMeta = $this->getMetaData($objSubfiles->meta, $objPage->rootFallbackLanguage);
						}
					}

					// Use the file name as title if none is given
					if ($arrMeta['title'] == '')
					{
						$arrMeta['title'] = specialchars($objFile->basename);
					}

					// Add the image
					$images[$objSubfiles->path] = array
					(
						'id'        => $objSubfiles->id,
						'uuid'      => $objSubfiles->uuid,
						'name'      => $objFile->basename,
						'singleSRC' => $objSubfiles->path,
						'alt'       => $arrMeta['title'],
						'imageUrl'  => $arrMeta['link'],
						'caption'   => $arrMeta['caption']
					);

					$auxDate[] = $objFile->mtime;
				}
			}
		}

		// Sort array
		switch ($this->sortBy)
		{
			default:
			case 'name_asc':
				uksort($images, 'basename_natcasecmp');
				break;

			case 'name_desc':
				uksort($images, 'basename_natcasercmp');
				break;

			case 'date_asc':
				array_multisort($images, SORT_NUMERIC, $auxDate, SORT_ASC);
				break;

			case 'date_desc':
				array_multisort($images, SORT_NUMERIC, $auxDate, SORT_DESC);
				break;

			case 'meta': // Backwards compatibility
			case 'custom':
				if ($objAlbum->orderSRC != '')
				{
					$tmp = deserialize($objAlbum->orderSRC);

					if (!empty($tmp) && is_array($tmp))
					{
						// Remove all values
						$arrOrder = array_map(function(){}, array_flip($tmp));

						// Move the matching elements to their position in $arrOrder
						foreach ($images as $k=>$v)
						{
							if (array_key_exists($v['uuid'], $arrOrder))
							{
								$arrOrder[$v['uuid']] = $v;
								unset($images[$k]);
							}
						}

						// Append the left-over images at the end
						if (!empty($images))
						{
							$arrOrder = array_merge($arrOrder, array_values($images));
						}

						// Remove empty (unreplaced) entries
						$images = array_values(array_filter($arrOrder));
						unset($arrOrder);
					}
				}
				break;

			case 'random':
				shuffle($images);
				break;
		}

		$images = array_values($images);

		$arrBody = array();



		foreach ($images as $image)
		{
			$objCell = new \stdClass();

			// Add size and margin
			$image['size'] = $this->imgSize;
			$image['fullsize'] = $this->fullsize;
			$strLightboxId = 'lightbox[lb' . $this->id . ']';

			$this->addImageToTemplate($objCell, $image, null, $strLightboxId);

			$arrBody[] = $objCell;

		}

		$objTemplate->body = $arrBody;

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
			$arrAlbums[] = $this->parseAlbum($objAlbums, $blnAddCategory, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % $this->item_perRow) == 0) ? ' last_col' : '') . ((($count % $this->item_perRow) == 1) ? ' first_col' : ''), $count);
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

		/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseImage($objAlbum,$imgSize)
	{

		$arrAlbum = array();

		$objModel = \FilesModel::findByUuid($objAlbum->singleSRC);

		if ($objModel !== null and is_file(TL_ROOT . '/' . $objModel->path))
		{
			// Do not override the field now that we have a model registry (see #6303)
			$arrAlbum = $objAlbum->row();

			// Override the default image size
			if ($imgSize != '')
			{
				$size = deserialize($imgSize);

				if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
				{
					$arrAlbum['size'] = $imgSize;
				}
			}

			$arrAlbum['singleSRC'] = $objModel->path;
		}

		return($arrAlbum);

	}


}
