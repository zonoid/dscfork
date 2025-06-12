<?php
/**
 * @version		$Id: element.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
/**
 * @version		$Id: element.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Log\Log;

/**
 * Content Component User Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since		1.5
 */
class SampleModelElementUser extends ListModel
{
	/**
	 * Content data in category array
	 *
	 * @var array
	 */
	protected $_list = null;

	protected $_page = null;

	/**
	 * Method to get content article data for the frontpage
	 *
	 * @since 1.5
	 */
	function getList()
	{
		$where = array();
		$app = Factory::getApplication(); // Changed $mainframe to $app

		if (!empty($this->_list)) {
			return $this->_list;
		}

		// Initialize variables
		$db		= $this->getDbo(); // Use $this->getDbo() from ListModel
		$filter	= null;

		// Get some variables from the request
		$sectionid 			= $app->input->getInt( 'sectionid', -1 ); // Use $app
		$redirect			= $sectionid;
		$option 			= $app->input->getCmd( 'option' ); // Use $app
		$filter_order		= $app->getUserStateFromRequest('userelement.filter_order',		'filter_order',		'',	'cmd'); // Use $app
		$filter_order_Dir	= $app->getUserStateFromRequest('userelement.filter_order_Dir',	'filter_order_Dir',	'',	'word'); // Use $app
		$limit				= $app->getUserStateFromRequest('global.list.limit',					'limit', $app->getCfg('list_limit'), 'int'); // Use $app
		$limitstart			= $app->getUserStateFromRequest('userelement.limitstart',			'limitstart',		0,	'int'); // Use $app
		$search				= $app->getUserStateFromRequest('userelement.search',				'search',			'',	'string'); // Use $app
		$search				= strtolower($search); // Replaced JString::strtolower

		if (!$filter_order) {
			$filter_order = 'id';
		}
		$order = ' ORDER BY '. $filter_order .' '. $filter_order_Dir;
		$all = 1;

		// Keyword filter
		if ($search) {
			$where[] = 'LOWER( c.id ) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( c.name ) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( c.username ) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
			$where[] = 'LOWER( c.email ) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false );
		}
		// Build the where clause of the query
		$where = (count($where) ? ' WHERE '.implode(' OR ', $where) : '');

		// Get the total number of records
		$query = 'SELECT COUNT(*)' .
				' FROM #__users AS c' .
				$where;
		$db->setQuery($query);
		$total = $db->loadResult();

		// Create the pagination object
		$this->_page = new Pagination($total, $limitstart, $limit);

		// Get the users
		$query = 'SELECT c.*' .
				' FROM #__users AS c' .
				$where .
				$order;
		$db->setQuery($query, $this->_page->limitstart, $this->_page->limit);
		$this->_list = $db->loadObjectList();

		// If there is a db query error, throw a HTTP 500 and exit
		if ($db->getErrorNum()) {
			Factory::getApplication()->enqueueMessage($db->stderr(), 'error');
			Log::add($db->stderr(), Log::ERROR, 'database'); // Optional: Log the error
			return false;
		}

		return $this->_list;
	}

	/**
	 * 
	 * @return unknown_type
	 */
	function getPagination()
	{
		if (is_null($this->_list) || is_null($this->_page)) {
			$this->getList();
		}
		return $this->_page;
	}
	
	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function _fetchElement($name, $value='', $node='', $control_name='')
	{
		$app = Factory::getApplication(); // Changed $mainframe to $app

		$db			= $this->getDbo(); // Use $this->getDbo()
		$doc 		= Factory::getApplication()->getDocument();
		$template 	= $app->getTemplate(); // Use $app
		$fieldName	= $control_name ? $control_name.'['.$name.']' : $name;
		
		if ($value) {
			$user = Factory::getUser( $value ); // JFactory::getUser for specific user ID is okay
			$title = $user->username;
		} else {
			$title = Text::_('LIB_STRATUM_SELECT_A_USER');
		}
		
		$js = "
		function jSelectUser(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_sample&task=elementUser&tmpl=component&object='.$name;

		HTMLHelper::_( 'behavior.modal', 'a.modal' );
		$html = "\n".'<input type="text" id="'.$name.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" />';
		$html .= '<a class="btn btn-primary modal" style="color : white; margin-left : 2px;" title="'.Text::_('Select a User').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.Text::_('LIB_STRATUM_SELECT').'</a>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function _clearElement($name, $value='', $node='', $control_name='')
	{
		
		$app = Factory::getApplication(); // Changed $mainframe to $app

		$db			= $this->getDbo(); // Use $this->getDbo()
		$doc 		= Factory::getApplication()->getDocument();
		$template 	= $app->getTemplate(); // Use $app
		$fieldName	= $control_name ? $control_name.'['.$name.']' : $name;
		
		$js = "
		function resetElement(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
		}";
		$doc->addScriptDeclaration($js);
		
		$html = '<a class="btn btn-danger" style="color : white;" href="javascript::void();" onclick="resetElement( \''.$value.'\', \''.Text::_( 'Select a User' ).'\', \''.$name.'\' )">'.Text::_( 'Clear Selection' ).'</à>'."\n";

		return $html;
	}
	
}
// Removed extra ?>
