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

jimport( 'joomla.filter.filteroutput' );
jimport( 'joomla.application.component.view' );

class StratumView extends JViewLegacy
{
	var $_option = NULL;
	var $_name = NULL;
	protected $_doTask = null;

	function __construct( $config = array() )
	{
		$app = Stratum::getApp( );
		$this->_option = !empty( $app ) ? 'com_' . $app->getName( ) : JFactory::getApplication()->input->getCmd('option');
		parent::__construct( $config );
	}

	/**
	 * Sets the task to something valid
	 *
	 * @access   public
	 * @param    string $task The task name.
	 * @return   string Previous value
	 * @since    1.5
	 */
	public function setTask( $task )
	{
		$previous = $this->_doTask;
		$this->_doTask = $task;
		return $previous;
	}

	/**
	 *
	 * Enter description here ...
	 * @return string
	 */
	public function getTask( )
	{
		return $this->_doTask;
	}

	/**
	 * Displays a layout file
	 *
	 * @param unknown_type $tpl
	 * @return unknown_type
	 */
	function display( $tpl = null )
	{
		// display() will return null if 'doTask' is not set by the controller
		// This prevents unauthorized access by bypassing the controllers
		$task = $this->getTask( );
		if( empty( $task ) )
		{
			return null;
		}

		parent::display( $tpl );
	}

	/**
	 * Gets layout vars for the view
	 *
	 * @return unknown_type
	 */
	function getLayoutVars( $tpl = null )
	{
		$layout = $this->getLayout( );
		switch(strtolower($layout))
		{
			case "view" :
				$this->_form( $tpl );
				break;
			case "form" :
				JFactory::getApplication()->input->set('hidemainmenu', '1');
				$this->_form( $tpl );
				break;
			case "default" :
			default :
				$this->_default( $tpl );
				break;
		}
	}

	/**
	 * Basic commands for displaying a list
	 *
	 * @param $tpl
	 * @return unknown_type
	 */
	function _default( $tpl = '' )
	{
		$model = $this->getModel( );

		// page-navigation
		if( empty( $this->no_pagination ) && method_exists( $model, 'getPagination' ) )
		{
			$this->pagination = $model->getPagination( );
		}

		// list of items
		if( empty( $this->no_items ) && method_exists( $model, 'getList' ) )
		{
			$this->items = $model->getList( );
		}
		// set the model state
		$state = new JObject( );
		if( empty( $this->no_state ) && method_exists( $model, 'getState' ) )
		{
			$state = $model->getState( );
		}
		JFilterOutput::objectHTMLSafe( $state );		
		$this->state = $state;

		// form
		$validate = JSession::getFormToken( );

		$form = array( );
		$view = JFactory::getApplication()->input->getCmd('view');
		$view = strtolower( $view );
		$form[ 'action' ] = $this->get( '_action', "index.php?option={$this->_option}&controller={$view}&view={$view}" );
		$form[ 'validate' ] = "<input type='hidden' name='{$validate}' value='1' />";
		$this->form = $form;
	}

	/**
	 * Basic methods for a form
	 * @param $tpl
	 * @return unknown_type
	 */
	function _form( $tpl = '' )
	{
		$model = $this->getModel( );
		$input = JFactory::getApplication( )->input;

		// get the data
		$row = $model->getItem( );
		JFilterOutput::objectHTMLSafe( $row );
		$this->row = $row;

		// form
		$form = array( );
		$controller = strtolower( $this->get( '_controller', $input->getCmd( 'controller', $input->getCmd( 'view' ) ) ) );
		$view = strtolower( $this->get( '_view', $input->getCmd( 'view' ) ) );
		$task = strtolower( $this->get( '_task', 'edit' ) );
		$form[ 'action' ] = $this->get( '_action', "index.php?option={$this->_option}&controller={$controller}&view={$view}&task={$task}&id=" . $model->getId( ) );
		$form[ 'validation' ] = $this->get( '_validation', "index.php?option={$this->_option}&controller={$controller}&view={$view}&task=validate&format=raw" );

		$validate = JSession::getFormToken( );
		$form[ 'validate' ] = "<input type='hidden' name='" . $validate . "' value='1' />";

		$form[ 'id' ] = $model->getId( );
		$this->form = $form;
	}

	/**
	 * The default toolbar for a list
	 * @return unknown_type
	 */
	function _defaultToolbar( )
	{
	}

	/**
	 * The default toolbar for editing an item
	 * @param $isNew
	 * @return unknown_type
	 */
	function _formToolbar( $isNew = null )
	{
	}

	/**
	 * The default toolbar for viewing an item
	 * @param $isNew
	 * @return unknown_type
	 */
	function _viewToolbar( $isNew = null )
	{
	}

}
