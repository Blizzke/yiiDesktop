<?php
/**
 *
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign.9maand.com
 * @category
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
		return CHtml::tag('li', array(), CHtml::link($this->name, '#icon_dock_' . $this->id, array('class' => $this->id . ' application')));
	}
}