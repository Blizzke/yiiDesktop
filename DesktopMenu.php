<?php
/**
 *
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign.9maand.com
 * @category
 * @copyright 2013 B&E DeZign
 */


class DesktopMenu extends CComponent
{
	protected $_oDesktop = NULL;
	protected $_aItems   = array();
	protected $_sTitle   = NULL;

	public function __construct($sTitle = NULL)
	{
		$this->title = $sTitle;
	}

	public function addItem(DesktopMenuItem $oItem)
	{
		$oItem->menu = $this;
		$this->_aItems[] = $oItem;
	}

	public function addFromShortCut(DesktopShortCut $oShortCut)
	{
		// Don't bother copying the route as it will point to the window of the shortcut, which already contains it
		$oItem = new DesktopMenuItem($oShortCut->name, $oShortCut->id, $oShortCut->icon);
		$this->addItem($oItem);
	}

	public function setDesktop(Desktop $oDesktop)
	{
		$this->_oDesktop = $oDesktop;
		foreach ($this->_aItems as $oItem)
			$oItem->desktop = $oDesktop;
	}

	public function setTitle($sTitle)   { $this->_sTitle = $sTitle; }
	public function getTitle()          { return $this->_sTitle; }
	public function getItems()          { return $this->_aItems; }

	public function render()
	{
		$sResult = CHtml::openTag('li') .
			CHtml::link($this->title, '#', array('class' => 'menu_trigger')) .
			CHtml::openTag('ul', array('class' => 'menu'));

		foreach ($this->_aItems as $oItem)
			$sResult .= $oItem->render();

		$sResult .= CHtml::closeTag('ul') . CHtml::closeTag('li');
		return $sResult;
	}
}