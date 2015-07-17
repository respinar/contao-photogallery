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
 * Table tl_photogallery_album
 */
$GLOBALS['TL_DCA']['tl_photogallery_album'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_photogallery',
		'enableVersioning'            => true,
		'onload_callback' => array
		(
			array('tl_photogallery_album', 'checkPermission'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id'    => 'primary',
				'pid'   => 'index',
				'alias' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('date DESC'),
			'headerFields'            => array('title', 'jumpTo','protected'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_photogallery_album', 'listAlbums'),
			'child_record_class'      => 'no_padding'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_photogallery_album']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_photogallery_album']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_photogallery_album']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_photogallery_album']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_photogallery_album', 'toggleIcon')
			),
			'feature' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_photogallery_album']['feature'],
				'icon'                => 'featured.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleFeatured(this,%s)"',
				'button_callback'     => array('tl_photogallery_album', 'iconFeatured')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_photogallery_album']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('published'),
		'default'                     => '{title_legend},title,featured,alias,author;{meta_legend},location,photographer,date,time;{poster_legend},singleSRC;{album_legend},multiSRC;{teaser_legend},teaser;{seo_legend:hide},description;{publish_legend},published'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'published'                   => 'start,stop'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_photogallery.title',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['title'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255,'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['alias'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true,'rgxp'=>'alias', 'unique'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
		),
		'author' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['author'],
			'default'                 => BackendUser::getInstance()->id,
			'exclude'                 => true,
			'search'                  => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'inputType'               => 'select',
			'foreignKey'              => 'tl_user.name',
			'eval'                    => array('doNotCopy'=>true, 'chosen'=>true, 'mandatory'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'hasOne', 'load'=>'eager')
		),
		'featured' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['featured'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['date'],
			'default'                 => time(),
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 8,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'date', 'doNotCopy'=>true, 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'time' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['time'],
			'default'                 => time(),
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'time', 'doNotCopy'=>true, 'tl_class'=>'w50'),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'location' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['location'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255,'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'photographer' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['photographer'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255,'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'extensions'=>Config::get('validImageTypes'), 'fieldType'=>'radio', 'mandatory'=>true),
			'sql'                     => "binary(16) NULL"
		),
		'multiSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['multiSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('multiple'=>true, 'fieldType'=>'checkbox', 'orderField'=>'orderSRC', 'files'=>true, 'mandatory'=>true),
			'sql'                     => "blob NULL",
			'load_callback' => array
			(
				array('tl_photogallery_album', 'setMultiSrcFlags')
			)
		),
		'orderSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['orderSRC'],
			'sql'                     => "blob NULL"
		),
		'teaser' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['teaser'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('rte'=>'tinyMCE', 'tl_class'=>'clr'),
			'sql'                     => "text NULL"
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['description'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'search'                  => true,
			'eval'                    => array('style'=>'height:60px', 'decodeEntities'=>true, 'tl_class'=>'clr'),
			'sql'                     => "text NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true, 'doNotCopy'=>true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'start' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['start'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'stop' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_photogallery_album']['stop'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		)
	)
);


/**
 * Class tl_photogallery_album
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    Core
 */
class tl_photogallery_album extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Check permissions to edit table tl_photogallery_album
	 */
	public function checkPermission()
	{
		// HOOK: comments extension required
		if (!in_array('comments', ModuleLoader::getActive()))
		{
			$key = array_search('allowComments', $GLOBALS['TL_DCA']['tl_photogallery_album']['list']['sorting']['headerFields']);
			unset($GLOBALS['TL_DCA']['tl_photogallery_album']['list']['sorting']['headerFields'][$key]);
		}

		if ($this->User->isAdmin)
		{
			return;
		}

		// Set the root IDs
		if (!is_array($this->User->photogallerys) || empty($this->User->photogallerys))
		{
			$root = array(0);
		}
		else
		{
			$root = $this->User->photogallerys;
		}

		$id = strlen(Input::get('id')) ? Input::get('id') : CURRENT_ID;

		// Check current action
		switch (Input::get('act'))
		{
			case 'paste':
				// Allow
				break;

			case 'create':
				if (!strlen(Input::get('pid')) || !in_array(Input::get('pid'), $root))
				{
					$this->log('Not enough permissions to create album items in photogallery ID "'.Input::get('pid').'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'cut':
			case 'copy':
				if (!in_array(Input::get('pid'), $root))
				{
					$this->log('Not enough permissions to '.Input::get('act').' album item ID "'.$id.'" to photogallery ID "'.Input::get('pid').'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				// NO BREAK STATEMENT HERE

			case 'edit':
			case 'show':
			case 'delete':
			case 'toggle':
			case 'feature':
				$objArchive = $this->Database->prepare("SELECT pid FROM tl_photogallery_album WHERE id=?")
											 ->limit(1)
											 ->execute($id);

				if ($objArchive->numRows < 1)
				{
					$this->log('Invalid album item ID "'.$id.'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}

				if (!in_array($objArchive->pid, $root))
				{
					$this->log('Not enough permissions to '.Input::get('act').' album item ID "'.$id.'" of photogallery ID "'.$objArchive->pid.'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'select':
			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
			case 'cutAll':
			case 'copyAll':
				if (!in_array($id, $root))
				{
					$this->log('Not enough permissions to access photogallery ID "'.$id.'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}

				$objArchive = $this->Database->prepare("SELECT id FROM tl_photogallery_album WHERE pid=?")
											 ->execute($id);

				if ($objArchive->numRows < 1)
				{
					$this->log('Invalid photogallery ID "'.$id.'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}

				$session = $this->Session->getData();
				$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objArchive->fetchEach('id'));
				$this->Session->setData($session);
				break;

			default:
				if (strlen(Input::get('act')))
				{
					$this->log('Invalid command "'.Input::get('act').'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				elseif (!in_array($id, $root))
				{
					$this->log('Not enough permissions to access photogallery ID ' . $id, __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}

	/**
	 * Add the type of input field
	 * @param array
	 * @return string
	 */
	public function listAlbums($arrRow)
	{

		$objImage = \FilesModel::findByPk($arrRow['singleSRC']);

		if ($objImage !== null)
		{
			$strImage = \Image::getHtml(\Image::get($objImage->path, '80', '60', 'center_center'));
		}

		return '<div style="float:left; margin-right:10px;">'.$strImage.'</div><div class="tl_content_left">' . $arrRow['title'] . ' <p style="color:#b3b3b3;clear:right;padding-top:5px">[' . Date::parse(Config::get('datimFormat'), $arrRow['date']) . ']</p></div>';
	}

	/**
	 * Dynamically add flags to the "multiSRC" field
	 * @param mixed
	 * @param \DataContainer
	 * @return mixed
	 */
	public function setMultiSrcFlags($varValue, DataContainer $dc)
	{
		if ($dc->activeRecord)
		{

			$GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['eval']['isGallery'] = true;
			$GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['eval']['extensions'] = Config::get('validImageTypes');

		}

		return $varValue;
	}


	/**
	 * Return the "feature/unfeature element" button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function iconFeatured($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(Input::get('fid')))
		{
			$this->toggleFeatured(Input::get('fid'), (Input::get('state') == 1));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the fid, so hacking attempts are logged
		if (!$this->User->hasAccess('tl_photogallery_album::featured', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;fid='.$row['id'].'&amp;state='.($row['featured'] ? '' : 1);

		if (!$row['featured'])
		{
			$icon = 'featured_.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}


	/**
	 * Feature/unfeature a alnum item
	 *
	 * @param integer $intId
	 * @param boolean $blnVisible
	 *
	 * @return string
	 */
	public function toggleFeatured($intId, $blnVisible)
	{
		// Check permissions to edit
		Input::setGet('id', $intId);
		Input::setGet('act', 'feature');
		$this->checkPermission();

		// Check permissions to feature
		if (!$this->User->hasAccess('tl_photogallery_album::featured', 'alexf'))
		{
			$this->log('Not enough permissions to feature/unfeature album item ID "'.$intId.'"', __METHOD__, TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$objVersions = new Versions('tl_photogallery_album', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_photogallery_album']['fields']['featured']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_photogallery_album']['fields']['featured']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, $this);
				}
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_photogallery_album SET tstamp=". time() .", featured='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$objVersions->create();
		$this->log('A new version of record "tl_photogallery_album.id='.$intId.'" has been created'.$this->getParentEntries('tl_photogallery_album', $intId), __METHOD__, TL_GENERAL);
	}


	/**
	 * Return the "toggle visibility" button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(Input::get('tid')))
		{
			$this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->hasAccess('tl_photogallery_album::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}


	/**
	 * Disable/enable a user group
	 *
	 * @param integer       $intId
	 * @param boolean       $blnVisible
	 * @param DataContainer $dc
	 */
	public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
	{
		// Check permissions to edit
		Input::setGet('id', $intId);
		Input::setGet('act', 'toggle');
		$this->checkPermission();

		// Check permissions to publish
		if (!$this->User->hasAccess('tl_photogallery_album::published', 'alexf'))
		{
			$this->log('Not enough permissions to publish/unpublish album item ID "'.$intId.'"', __METHOD__, TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$objVersions = new Versions('tl_photogallery_album', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_photogallery_album']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_photogallery_album']['fields']['published']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, ($dc ?: $this));
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, ($dc ?: $this));
				}
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_photogallery_album SET tstamp=". time() .", published='" . ($blnVisible ? '1' : '') . "' WHERE id=?")
					   ->execute($intId);

		$objVersions->create();
		$this->log('A new version of record "tl_photogallery_album.id='.$intId.'" has been created'.$this->getParentEntries('tl_photogallery_album', $intId), __METHOD__, TL_GENERAL);

	}

}
