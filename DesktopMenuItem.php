<?php
/**
 * Menu item class
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign
 * @copyright 2013 B&E DeZign
 */

class DesktopMenuItem extends DesktopApplication
{
	protected $_oMenu = NULL;

	public function setMenu(DesktopMenu $oMenu)
	{
		$this->_oMenu = $oMenu;
	}

	public function render()
	{
		return CHtml::tag('li', array(), CHtml::link($this->title, '#icon_dock_' . $this->id, array('class' => $this->id . ' application')));
	}
}