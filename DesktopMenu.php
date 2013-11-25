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
	protected $_sName    = NULL;

	public function __construct($sTitle)
	{
		$this->_sName = $sTitle;
	}

	public function addItem(DesktopMenuItem $oItem)
	{
		$oItem->menu = $this;
		$this->_aItems[] = $oItem;
	}

	public function addFromShortCut(DesktopShortCut $oShortCut)
	{
		$oItem = new DesktopMenuItem($oShortCut->name, $oShortCut->id);

		foreach (array('icon') as $sProperty)
			$oItem->$sProperty = $oShortCut->$sProperty;

		$this->addItem($oItem);
	}

	public function setDesktop(Desktop $oDesktop)
	{
		$this->_oDesktop = $oDesktop;
		foreach ($this->_aItems as $oItem)
			$oItem->desktop = $oDesktop;
	}

	public function getName()
	{
		return $this->_sName;
	}

	public function getItems()
	{
		return $this->_aItems;
	}

	public function render()
	{
		$sResult = CHtml::openTag('li') .
			CHtml::link($this->name, '#', array('class' => 'menu_trigger')) .
			CHtml::openTag('ul', array('class' => 'menu'));

		foreach ($this->_aItems as $oItem)
			$sResult .= $oItem->render();

		$sResult .= CHtml::closeTag('ul') . CHtml::closeTag('li');
		return $sResult;
	}
}