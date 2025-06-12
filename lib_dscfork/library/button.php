<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
// TODO: J4/5 Consider if specific Joomla CMS classes should be imported via `use` statements here.
// use Joomla\CMS\Toolbar\Toolbar;
// use Joomla\CMS\Toolbar\ToolbarButton;
// use Joomla\CMS\Language\Text;
// use Joomla\String\StringHelper; // If JString::strtolower is used, though it might be StringHelper::strtolower

/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

// TODO: J4/5 Replace jimport with 'use' statement.
//jimport( 'joomla.html.toolbar.button' );

class StratumButton extends JButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Stratum';

	function fetchButton( $type = 'Stratum', $name = '', $text = '', $task = '', $list = true, $hideMenu = false, $taskName = 'shippingTask' )
	{
		$i18n_text = JText::_( $text );
		$class = $this->fetchIconClass( $name );
		$doTask = $this->_getCommand( $text, $task, $list, $hideMenu, $taskName );

		$html = "<a href=\"#\" onclick=\"$doTask\" class=\"toolbar\">\n";
		$html .= "<span class=\"$class\" title=\"$i18n_text\">\n";
		$html .= "</span>\n";
		$html .= "$i18n_text\n";
		$html .= "</a>\n";

		return $html;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @access	private
	 * @param	string	$name	The task name as seen by the user
	 * @param	string	$task	The task used by the application
	 * @param	???		$list
	 * @param	boolean	$hide
	 * @param	string	$taskName	the task field name
	 * @return	string	JavaScript command string
	 * @since	1.5
	 */
	function _getCommand( $name, $task, $list, $hide, $taskName )
	{
		$todo = JString::strtolower( JText::_( $name ) );
		$message = JText::sprintf( 'LIB_STRATUM_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST_TO_DO', $todo );
		$message = addslashes( $message );

		if ( $list )
		{
			$cmd = "javascript:if(document.adminForm.boxchecked.value==0){alert('$message');}else{ submitStratumbutton('$task', '$taskName')}";
		} else
		{
			$cmd = "javascript:$hidecode submitStratumbutton('$task', '$taskName')";
		}

		return $cmd;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @access	public
	 * @return	string	Button CSS Id
	 */
	function fetchId( $type = 'Confirm', $name = '', $text = '', $task = '', $list = true, $hideMenu = false )
	{
		return $this->_name . '-' . $name;
	}

}
