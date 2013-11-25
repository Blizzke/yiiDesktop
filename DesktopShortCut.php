<?php
/**
 *
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign.9maand.com
 * @category
 * @copyright 2013 B&E DeZign
 */


class DesktopShortCut extends DesktopApplication
{
	protected $_nX       = NULL;

	public function getX()
	{
		return $this->_oDesktop->getX(NULL);
	}

	public function getY()
	{
		return $this->_oDesktop->getY(NULL);
	}

	public function render()
	{
		$aOptions = array
		(
			'class' => $this->_oDesktop->cssClass . ' icon ' . $this->_sId,
			'style' => "left: {$this->x}px; top: {$this->y}px",
		);

		return CHtml::link(CHtml::image($this->_sIcon, $this->_sName, array('height' => '32px')) . $this->_sName, '#icon_dock_' . $this->_sId, $aOptions);
	}

}