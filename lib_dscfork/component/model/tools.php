<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	component/model
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

Sample::load( 'SampleModelBase', 'models._base' );

class SampleModelTools extends SampleModelBase
{
	protected function _buildQueryWhere( &$query )
	{
		$filter = $this->getState( 'filter' );
		$filter_id_from = $this->getState( 'filter_id_from' );
		$filter_id_to = $this->getState( 'filter_id_to' );
		$filter_name = $this->getState( 'filter_name' );

		if( $filter )
		{
			$key = $this->_db->Quote( '%' . $this->_db->escape( trim( strtolower( $filter ) ) ) . '%' );

			$where = array( );
			$where[ ] = 'LOWER(tbl.id) LIKE ' . $key;
			$where[ ] = 'LOWER(tbl.name) LIKE ' . $key;
			$where[ ] = 'LOWER(tbl.element) LIKE ' . $key;

			$query->where( '(' . implode( ' OR ', $where ) . ')' );
		}

		if( strlen( $filter_id_from ) )
		{
			if( strlen( $filter_id_to ) )
			{
				$query->where( 'tbl.id >= ' . (int)$filter_id_from );
			} else
			{
				$query->where( 'tbl.id = ' . (int)$filter_id_from );
			}
		}

		if( strlen( $filter_id_to ) )
		{
			$query->where( 'tbl.id <= ' . (int)$filter_id_to );
		}
		if( $filter_name )
		{
			$key = $this->_db->Quote( '%' . $this->_db->escape( trim( strtolower( $filter_name ) ) ) . '%' );
			$where = array( );
			$where[ ] = 'LOWER(tbl.name) LIKE ' . $key;
			$query->where( '(' . implode( ' OR ', $where ) . ')' );
		}

		$query->where( "LOWER(tbl.folder) = 'sample'" );
		$query->where( "tbl.element LIKE 'tool_%'" );
	}

	public function getList( )
	{
		$list = parent::getList( );
		foreach( $list as $item )
		{
			$item->link = 'index.php?option=com_sample&view=tools&task=view&id=' . $item->id;
		}
		return $list;
	}

}
