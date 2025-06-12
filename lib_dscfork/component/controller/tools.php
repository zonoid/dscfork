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

class StratumControllerTools extends StratumController
{
	/**
	 * constructor
	 */
	function __construct( )
	{
		parent::__construct( );

		$this->set( 'suffix', 'tools' );
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState( )
	{
		$state = parent::_setModelState( );
		$app = JFactory::getApplication( );
		$model = $this->getModel( $this->get( 'suffix' ) );
		$ns = $this->getNamespace( );

		$state[ 'filter_id_from' ] = $app->getUserStateFromRequest( $ns . 'id_from', 'filter_id_from', '', '' );
		$state[ 'filter_id_to' ] = $app->getUserStateFromRequest( $ns . 'id_to', 'filter_id_to', '', '' );
		$state[ 'filter_name' ] = $app->getUserStateFromRequest( $ns . 'name', 'filter_name', '', '' );

		foreach( @$state as $key => $value )
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
	 * Displays item
	 * @return void
	 */
	function view( )
	{
		$model = $this->getModel( $this->get( 'suffix' ) );
		$model->getId( );
		$row = $model->getItem( );

		if( empty( $row->published ) )
		{
			$table = $model->getTable( );
			$table->load( $row->id );
			$table->published = 1;
			if( $table->save( ) )
			{
				$redirect = "index.php?option=com_sample&view=" . $this->get( 'suffix' ) . "&task=view&id=" . $model->getId( );
				$redirect = JRoute::_( $redirect, false );
				$this->setRedirect( $redirect );
				return;
			}
		}

		parent::view( );
	}

}
