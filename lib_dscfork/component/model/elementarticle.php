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
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Table\Table;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Log\Log;

//TODO: UPDATE MODAL ELEMENT WITH THE LATEST JOOMLA ELEMENT MODAL

/**
 * Content Component Article Model
 *
 * @package		Joomla
 * @subpackage	Content
 * @since		1.5
 */
class SampleModelElementArticle extends ListModel
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
	function getList( )
	{
		$app = Factory::getApplication( ); // Changed $mainframe to $app for clarity

		if( !empty( $this->_list ) )
		{
			return $this->_list;
		}

		// Initialize variables
		$db = $this->getDbo( ); // Use $this->getDbo() from ListModel
		$filter = null;

		// Get some variables from the request
		$sectionid = $app->input->getInt( 'sectionid', -1 ); // Use $app
		$redirect = $sectionid;
		$option = $app->input->getCmd( 'option' ); // Use $app
		$filter_order = $app->getUserStateFromRequest( 'articleelement.filter_order', 'filter_order', '', 'cmd' ); // Use $app
		$filter_order_Dir = $app->getUserStateFromRequest( 'articleelement.filter_order_Dir', 'filter_order_Dir', '', 'word' ); // Use $app
		$catid = $app->getUserStateFromRequest( 'articleelement.catid', 'catid', 0, 'int' ); // Use $app
		$filter_authorid = $app->getUserStateFromRequest( 'articleelement.filter_authorid', 'filter_authorid', 0, 'int' ); // Use $app
		$filter_sectionid = $app->getUserStateFromRequest( 'articleelement.filter_sectionid', 'filter_sectionid', -1, 'int' ); // Use $app
		$limit = $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg( 'list_limit' ), 'int' ); // Use $app
		$limitstart = $app->getUserStateFromRequest( 'articleelement.limitstart', 'limitstart', 0, 'int' ); // Use $app
		$search = $app->getUserStateFromRequest( 'articleelement.search', 'search', '', 'string' ); // Use $app
		$search = strtolower( $search ); // Replaced JString::strtolower

		//$where[] = "c.state >= 0";
		$where[ ] = "c.state != -2";

		if( !$filter_order )
		{
			$filter_order = 'section_name';
		}
		$order = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ', section_name, cc.name, c.ordering';
		$all = 1;

		if( $filter_sectionid >= 0 )
		{
			$filter = ' WHERE cc.section = ' . $db->Quote( $filter_sectionid );
		}
		$section->title = 'All Articles';
		$section->id = 0;

		/*
		 * Add the filter specific information to the where clause
		 */
		// Section filter
		if( $filter_sectionid >= 0 )
		{
			$where[ ] = 'c.sectionid = ' . (int)$filter_sectionid;
		}
		// Category filter
		if( $catid > 0 )
		{
			$where[ ] = 'c.catid = ' . (int)$catid;
		}
		// Author filter
		if( $filter_authorid > 0 )
		{
			$where[ ] = 'c.created_by = ' . (int)$filter_authorid;
		}

		// Only published articles
		$where[ ] = 'c.state = 1';

		// Keyword filter
		if( $search )
		{
			$where[ ] = 'LOWER( c.title ) LIKE ' . $db->Quote( '%' . $db->escape( $search, true ) . '%', false );
		}

		// Build the where clause of the content record query
		$where = (count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '');

		// Get the total number of records
		$query = 'SELECT COUNT(*)' . ' FROM #__content AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' . $where;
		$db->setQuery( $query );
		$total = $db->loadResult( );

		// Create the pagination object
		$this->_page = new Pagination( $total, $limitstart, $limit );

		// Get the articles
		$query = 'SELECT c.*, g.name AS groupname, cc.title as cctitle, u.name AS editor, f.content_id AS frontpage, s.title AS section_name, v.name AS author' . ' FROM #__content AS c' . ' LEFT JOIN #__categories AS cc ON cc.id = c.catid' . ' LEFT JOIN #__sections AS s ON s.id = c.sectionid' . ' LEFT JOIN #__groups AS g ON g.id = c.access' . ' LEFT JOIN #__users AS u ON u.id = c.checked_out' . ' LEFT JOIN #__users AS v ON v.id = c.created_by' . ' LEFT JOIN #__content_frontpage AS f ON f.content_id = c.id' . $where . $order;
		$db->setQuery( $query, $this->_page->limitstart, $this->_page->limit );
		$this->_list = $db->loadObjectList( );

		// If there is a db query error, throw a HTTP 500 and exit
		if( $db->getErrorNum( ) )
		{
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
	function getPagination( )
	{
		if( is_null( $this->_list ) || is_null( $this->_page ) )
		{
			$this->getList( );
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
	function _fetchElement( $name, $value = '', $node = '', $control_name = '' )
	{
		$app = Factory::getApplication( ); // Changed $mainframe to $app

		$db = $this->getDbo( ); // Use $this->getDbo()
		$doc = Factory::getApplication()->getDocument( );
		$template = $app->getTemplate( ); // Use $app
		$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;
		$article = Table::getInstance('Content', 'JTable', array('dbo' => $this->getDbo()));
		if( $value )
		{
			$article->load( $value );
			$title = $article->title;
		} else
		{
			$title = Text::_( 'LIB_STRATUM_SELECT_AN_ARTICLE' );
		}

		$js = "
		function jSelectArticle(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration( $js );

		$link = 'index.php?option=com_sample&task=elementArticle&tmpl=component&object=' . $name;

		HTMLHelper::_( 'behavior.modal', 'a.modal' );
		$html = "\n" . '<div style="float: left;"><input style="background: #ffffff;" type="text" id="' . $name . '_name" value="' . htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' ) . '" disabled="disabled" /></div>';
		// $html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".Text::_('Select')."\" />";
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="' . Text::_( 'Select an Article' ) . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">' . Text::_( 'LIB_STRATUM_SELECT' ) . '</a></div></div>' . "\n";
		$html .= "\n" . '<input type="hidden" id="' . $name . '_id" name="' . $fieldName . '" value="' . (int)$value . '" />';

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
	function _clearElement( $name, $value = '', $node = '', $control_name = '' )
	{

		$app = Factory::getApplication( ); // Changed $mainframe to $app

		$db = $this->getDbo( ); // Use $this->getDbo()
		$doc = Factory::getApplication()->getDocument( );
		$template = $app->getTemplate( ); // Use $app
		$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;

		$js = "
		function resetElement(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
		}";
		$doc->addScriptDeclaration( $js );

		$html = '<div class="button2-left">
		<div class="blank">
		<a href="javascript::void();" onclick="resetElement( \'' . $value . '\', \'' . Text::_( 'Select an Article' ) . '\', \'' . $name . '\' )">' . Text::_( 'Clear Selection' ) . '</a>
		</div></div>' . "\n";

		return $html;
	}

}
