<?php
/**
 * Widget that encapsulates the so called 'jQuery Desktop' by Nathan Smith.
 * See:
 * - http://desktop.sonspring.com/
 * - http://sonspring.com/journal/jquery-desktop
 * - https://github.com/nathansmith/jquery-desktop/
 *
 * Note that I've decided to actually include the javascript code and images as I've made slight modifications to
 * allow using the most recent jQuery versions.
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign
 * @copyright 2013 B&E DeZign
 */

class Desktop extends CWidget
{
	/** @var string  The main encapsulating class (for separating your CSS) */
	public $cssClass = 'abs';
	/** @var int     Distance (in pixels) between 2 icons on the desktop */
	public $gridSize = 80;
	/** @var int     Amount of icons to place next to each other before jumping to the next line  */
	public $columns  = 5;
	/** @var int     Amount of pixels to leave on the left before starting with the icons */
	public $marginX  = 10;
	/** @var int     Amount of pixels under the menu bar before starting with the icons  */
	public $marginY  = 10;

	// Settings for the "show desktop" icon in the bottom bar. Leave the icon on NULL to use the included one
	public $showDesktopTitle = 'Show Desktop';
	public $showDesktopId = 'show_desktop';
	public $showDesktopIcon = NULL;

	/** @var string   Image to use as wallpaper. Included one if NULL */
	public $wallpaper = NULL;

	/** @var bool     If TRUE, show 24h Clock */
	public $clock24   = TRUE;

	protected static $_aMenus = array();
	protected static $_aShortCuts = array();
	protected $_aRenderedWindows = array();

	protected $_nX = 0;
	protected $_nY = 0;

	public function init()
	{
		$sUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/assets');

		$aSettings = array
		(
			'wallpaper' => $this->wallpaper ?: $sUrl . '/images/gui/wallpaper.jpg',
			'clock24'   => $this->clock24
		);
		$sSettings = CJavaScript::jsonEncode((object)$aSettings);

		Yii::app()->clientScript
			->registerCoreScript('jquery')
			->registerCoreScript('jquery-ui')
			->registerScriptFile($sUrl . '/js/jquery.desktop.js', CClientScript::POS_END)
			->registerCSSFile($sUrl . '/css/reset.css')
			->registerCSSFile($sUrl . '/css/desktop.css')
			->registerScript('JQD-go', "JQD.go($sSettings);", CClientScript::POS_READY);

		if (!$this->showDesktopIcon)
			$this->showDesktopIcon = $sUrl . '/images/gui/icon_22_desktop.png';
	}

	public function run()
	{
		echo CHtml::openTag('div', array('class' => $this->cssClass, 'id' => 'wrapper'));

		$this->_renderDesktop();
		$this->_renderMenu();
		$this->_renderDock();

		echo CHtml::closeTag('div'); // #wrapper
	}

	public static function addMenu(DesktopMenu $oMenu)
	{
		return self::$_aMenus[] = $oMenu;
	}

	public static function addShortCut(DesktopShortCut $oShortCut)
	{
		return self::$_aShortCuts[] = $oShortCut;
	}

	/**
	 * Returns the "physical" X position for the icon. If no grid position is specified it will simply return the
	 * next available intersection. Please note that this function makes no attempt to prevent 2 icons from being
	 * on top of each other if you actually specify a position.
	 *
	 * @param   int $nPreferred   Preferred grid x-position (not pixels)
	 * @return  int Icon x-position (in pixels)
	 */
	public function getX($nPreferred = NULL)
	{
		if ($nPreferred)
			return $nPreferred * $this->gridSize + $this->marginX;

		$nPosition = $this->_nX * $this->gridSize;
		$this->_nX ++;
		if (!($this->_nX % $this->columns))
		{
			$this->_nX = 1;
			$this->_nY ++;
		}

		return $nPosition + $this->marginX;
	}

	/**
	 * @param int $nPreferred  Preferred grid y-position (not pixels)
	 * @return int Icon y-position (in pixels)
	 */
	public function getY($nPreferred = NULL)
	{
		if ($nPreferred)
			return $nPreferred * $this->gridSize + $this->marginY;

		return $this->_nY * $this->gridSize + $this->marginY;
	}

	protected function _renderDesktop()
	{
		echo CHtml::openTag('div', array('class' => $this->cssClass, 'id' => 'desktop'));

		foreach (self::$_aShortCuts as $oShortCut)
		{
			// Render the icon
			$oShortCut->desktop = $this;
			echo $oShortCut->render();
			// Render the hidden desktop window for the app
			echo $oShortCut->renderWindow();
			$this->_aRenderedWindows[$oShortCut->id] = $oShortCut;
		}

		echo CHtml::closeTag('div'); // #desktop
	}

	protected function _renderMenu()
	{
		echo CHtml::openTag('div', array('id' => 'bar_top', 'class' => $this->cssClass));
		echo CHtml::tag('span', array('class' => 'float_right', 'id' => 'clock'), '');

		echo CHtml::openTag('ul');

		$sApplicationWindows = '';
		foreach (self::$_aMenus as $oMenu)
		{
			$oMenu->desktop = $this;
			echo $oMenu->render($this);

			// Keep track of which windows still left to render
			foreach ($oMenu->items as $oItem)
				if (!isset($this->_aRenderedWindows[$oItem->id]) || !$this->_aRenderedWindows[$oItem->id])
				{
					$sApplicationWindows .= $oItem->renderWindow();
					$this->_aRenderedWindows[$this->id] = $oItem;
				}
		}

		echo CHtml::closeTag('ul'), CHtml::closeTag('div');

		// Also output the remainder of the windows
		echo $sApplicationWindows;
	}

	protected function _renderDock()
	{
		echo CHtml::openTag('div', array('id' => 'bar_bottom', 'class' => $this->cssClass));

		echo CHtml::link(CHtml::image($this->showDesktopIcon, $this->showDesktopTitle, array('height' => '22px')), '#',
			array('class' => 'float_left', 'id' => $this->showDesktopId, 'title' => $this->showDesktopTitle));

		echo CHtml::openTag('ul', array('id' => 'dock'));

		foreach ($this->_aRenderedWindows as $oItem)
			echo $oItem->renderDock();

		echo CHtml::closeTag('ul'), CHtml::closeTag('div');
	}
}