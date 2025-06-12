<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later *
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class StratumToolBarHelper extends JToolBarHelper
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	static protected $_name = 'custom';

	/**
	 * Writes a custom option and task button for the button bar
	 * @param string The task to perform (picked up by the switch($task) blocks
	 * @param string The image to display
	 * @param string The image to display when moused over
	 * @param string The alt text for the icon image
	 * @param boolean True if required to check that a standard list item is checked
	 * @param boolean True if required to include callinh hideMainMenu()
	 * @since 1.0
	 */
	public static function custom( $task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true, $x = false, $taskName = 'shippingTask' )
	{
		$bar = JToolBar::getInstance( 'toolbar' );

		//strip extension
		$icon = preg_replace( '#\.[^.]*$#', '', $icon );

		// Add a standard button
		$bar->appendButton( static::$_name, $icon, $alt, $task, $listSelect, $x, $taskName );
	}

	/**
	 * Writes the common 'new' icon for the button bar
	 * @param string An override for the task
	 * @param string An override for the alt text
	 * @since 1.0
	 */
	public static function addNew( $task = 'add', $alt = 'New', $taskName = 'shippingTask' )
	{
		$bar = JToolBar::getInstance( 'toolbar' );
		// Add a new button
		$bar->appendButton( static::$_name, 'new', $alt, $task, false, false, $taskName );
	}

}
