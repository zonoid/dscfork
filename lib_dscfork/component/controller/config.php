<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	component/controller
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class StratumControllerConfig extends StratumController
{
	/**
	 * constructor
	 */
	function __construct( )
	{
		parent::__construct( );

		$this->set( 'suffix', 'config' );
	}

	/**
	 * save a record
	 * @return void
	 */
	function save( )
	{
		$error = false;
		$errorMsg = "";
		$model = $this->getModel( $this->get( 'suffix' ) );
		$app = Stratum::getApp();
		$com = $app->getName();
		$config = $com::getInstance( );
		$properties = $config->getProperties( );

		foreach( @$properties as $key => $value )
		{
			unset( $row );
			$row = $model->getTable( 'config' );
			$newvalue = $this->input->getString( $key );
			$value_exists = array_key_exists( $key, $_POST );
			if( $value_exists && !empty( $key ) )
			{
				// proceed if newvalue present in request. prevents overwriting for non-existent values.
				$row->load( array( 'config_name' => $key ) );
				$row->config_name = $key;
				$row->value = $newvalue;

				if( !$row->save( ) )
				{
					$error = true;
					$errorMsg .= JText::_( "LIB_STRATUM_COULD_NOT_STORE" ) . " $key :: " . $row->getError( ) . " - ";
				}
			}
		}

		if( !$error )
		{
			$this->messagetype = 'message';
			$this->message = JText::_( 'LIB_STRATUM_SAVED' );

			$dispatcher = JDispatcher::getInstance( );
			$dispatcher->trigger( 'onAfterSave' . $this->get( 'suffix' ), array( $row ) );
		} else
		{
			$this->messagetype = 'notice';
			$this->message = JText::_( 'LIB_STRATUM_SAVE_FAILED' ) . " - " . $errorMsg;
		}

		$redirect = "index.php?option=com_sample";
		$task = $this->input->getCmd('task');
		switch ($task)
		{
			default :
				$redirect .= "&view=" . $this->get( 'suffix' );
				break;
		}

		$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

}