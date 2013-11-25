<?php
/**
 * Base class for an "application". This is a fancy way of saying "some stuff that can load within an IFrame"
 *
 * @author    Steve Guns <steve@bedezign.com>
 * @package   com.bedezign
 * @copyright 2013 B&E DeZign
 */

class DesktopApplication extends CComponent
{
	protected $_oDesktop    = NULL;
	protected $_sTitle      = NULL;
	protected $_sId         = NULL;
	protected $_sIcon       = NULL;
	protected $_sRoute      = NULL;
	protected $_aParameters = array();

	public function __construct($sTitle = NULL, $sId = NULL, $sIcon = NULL)
	{
		$this->title = $sTitle;
		$this->id    = $sId;
		$this->icon  = $sIcon;
	}

	public function setDesktop(Desktop $oDesktop)
	{
		$this->_oDesktop = $oDesktop;
	}

	public function setRoute($sRoute, $aParameters = array())
	{
		$this->_sRoute = $sRoute;
		$this->_aParameters = $aParameters;
	}

	public function setIcon($sIcon)     { $this->_sIcon = $sIcon; }
	public function getIcon()           { return $this->_sIcon; }
	public function setId($sId)         { $this->_sId = $sId; }
	public function getId()             { return $this->_sId; }
	public function setTitle($sTitle)   { $this->_sTitle = $sTitle; }
	public function getTitle()          { return $this->_sTitle; }

	/**
	 * Returns a "rendered" application window. We need to make one of these for every application (including menu items).
	 * Note that as opposed to the tech demo, we are using iframes here to allow for an "independent" application.
	 * @return string
	 */
	public function renderWindow()
	{
		$sClass = $this->_oDesktop->cssClass;

		return
			CHtml::tag('div', array('id' => 'window_' . $this->id, 'class' => $sClass . ' window window_big'),
				CHtml::tag('div', array('class' => 'abs window_inner'),
					CHtml::tag('div', array('class' => 'window_top'),
						CHtml::tag('span', array('class' => 'float_left'),
							($this->icon ? CHtml::image($this->icon, $this->title, array('height' => '16px')) . ' ' : '') .
							$this->title
						) .
						CHtml::tag('span', array('class' => 'float_right'),
							CHtml::link('', '#', array('class' => 'window_min')) .
							CHtml::link('', '#', array('class' => 'window_resize')) .
							CHtml::link('', '#icon_dock_' . $this->id, array('class' => 'window_close'))
						)
					) .

					CHtml::tag('div', array('class' => $sClass . ' window_frame window_frame_small'), $this->renderContents()) .

					CHtml::tag('div', array('class' => $sClass . ' window_bottom'), '')
				) .
				CHtml::tag('span', array('class' => $sClass . ' ui-resizable-handle ui-resizable-se'), '')
			);
	}

	/**
	 * This functions renders the actual window contents. By default this is an empty iframe with a data-url attribute.
	 * The javascript class looks for an iframe and will change its location to the specified url if found.
	 * You can override this function if you need to output literal HTML
	 *
	 * @return string
	 */
	public function renderContents()
	{
		// We specify this URL as data-url for the iframe.
		$sUrl = Yii::app()->controller->createUrl($this->_sRoute, $this->_aParameters);
		return CHtml::tag('iframe', array('name' => 'frame_' . $this->id, 'scrolling' => 'auto', 'class' => 'frame_frame', 'data-url' => $sUrl), '');
	}

	/**
	 * Return the rendered dock button.
	 * This element is used by all launching methods to link to the application window. Without this, no window.
	 *
	 * @return string
	 */
	public function renderDock()
	{
		return CHtml::tag('li', array('id' => 'icon_dock_' . $this->id), CHtml::link(CHtml::image($this->icon, '', array('height' => '22px')) . $this->title, '#window_' . $this->id));
	}
}