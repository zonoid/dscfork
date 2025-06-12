<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	component/table
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Sample::load( 'SampleTable', 'tables._base' );

class SampleTableConfig extends SampleTable
{

	function SampleTableConfig( &$db )
	{
		$tbl_key = 'config_name';
		$tbl_suffix = 'config';
		$this->set( '_suffix', $tbl_suffix );
		$name = "sample";

		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
	}

	function store( $updateNulls = true )
	{
		$k = 'config_id';

		if( intval( $this->$k ) > 0 )
		{
			$ret = $this->_db->updateObject( $this->_tbl, $this, $this->_tbl_key );
		} else
		{
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}
		if( !$ret )
		{
			$this->setError( get_class( $this ) . '::store failed - ' . $this->_db->getErrorMsg( ) );
			return false;
		} else
		{
			return true;
		}
	}

}
