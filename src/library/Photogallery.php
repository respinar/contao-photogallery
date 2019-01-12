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


/**
 * Namespace
 */
namespace Respinar\Photogallery;

use Respinar\Photogallery\Model\PhotogalleryAlbumModel;
use Respinar\Photogallery\Model\PhotogalleryModel;

/**
 * Class Photogallery
 *
 * @copyright  respinar 2014-2019
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class Photogallery extends \Frontend
{

	/**
	 * Add product items to the indexer
	 * @param array
	 * @param integer
	 * @param boolean
	 * @return array
	 */
	public function getSearchablePages($arrPages, $intRoot=0, $blnIsSitemap=false)
	{
		$arrRoot = array();

		if ($intRoot > 0)
		{
			$arrRoot = $this->Database->getChildRecords($intRoot, 'tl_page');
		}

		$time = time();
		$arrProcessed = array();

		// Get all catalog categories
		$objPhotogallery = PhotogalleryModel::findByProtected('');

		// Walk through each archive
		if ($objPhotogallery !== null)
		{
			while ($objPhotogallery->next())
			{
				// Skip catalog categories without target page
				if (!$objPhotogallery->jumpTo)
				{
					continue;
				}

				// Skip catalog categories outside the root nodes
				if (!empty($arrRoot) && !in_array($objPhotogallery->jumpTo, $arrRoot))
				{
					continue;
				}

				// Get the URL of the jumpTo page
				if (!isset($arrProcessed[$objPhotogallery->jumpTo]))
				{
					$objParent = \PageModel::findWithDetails($objPhotogallery->jumpTo);

					// The target page does not exist
					if ($objParent === null)
					{
						continue;
					}

					// The target page has not been published (see #5520)
					if (!$objParent->published || ($objParent->start != '' && $objParent->start > $time) || ($objParent->stop != '' && $objParent->stop < $time))
					{
						continue;
					}

					// The target page is exempt from the sitemap (see #6418)
					if ($blnIsSitemap && $objParent->sitemap == 'map_never')
					{
						continue;
					}

					// Set the domain (see #6421)
					$domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: \Environment::get('host')) . TL_PATH . '/';

					// Generate the URL
					$arrProcessed[$objPhotogallery->jumpTo] = $domain . $this->generateFrontendUrl($objParent->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/%s' : '/items/%s'), $objParent->language);
				}

				$strUrl = $arrProcessed[$objPhotogallery->jumpTo];

				// Get the items
				$objAlbum = PhotogalleryAlbumModel::findPublishedByPid($objPhotogallery->id);

				if ($objAlbum !== null)
				{
					while ($objAlbum->next())
					{
						$arrPages[] = $this->getLink($objAlbum, $strUrl);
					}
				}
			}
		}

		return $arrPages;
	}


	/**
	 * Return the link of a album
	 * @param object
	 * @param string
	 * @param string
	 * @return string
	 */
	protected function getLink($objItem, $strUrl)
	{
		// Link to the default page
		return sprintf($strUrl, (($objItem->alias != '' && !\Config::get('disableAlias')) ? $objItem->alias : $objItem->id));
	}


	public function albumURLInsertTags($strTag)
    {
		
        // Parameter abtrennen
        $arrSplit = explode('::', $strTag);

        if ($arrSplit[0] != 'album_url')
        {
            //nicht unser Insert-Tag
            return false;
        }

		// Parameter angegeben?
        if (isset($arrSplit[1]))
        {
            // Get the items
			if (($objAlbum = PhotogalleryAlbumModel::findPublishedByIdOrAlias($arrSplit[1])) === null)
			{
				return false;
			}

			$objPhotogallery  = PhotogalleryModel::findBy('id',$objAlbum->pid);

			$objParent = \PageModel::findWithDetails($objPhotogallery->jumpTo);

			// Set the domain (see #6421)
			$domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: \Environment::get('host')) . TL_PATH . '/';

			// Generate the URL
			$strUrl = $domain . $this->generateFrontendUrl($objParent->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/%s' : '/items/%s'), $objParent->language);
	
			$link = $this->getLink($objAlbum, $strUrl);

			return $link;
        }
        else
        {
            return 'Fehler! foo ohne Parameter!';
        }
    }
}
