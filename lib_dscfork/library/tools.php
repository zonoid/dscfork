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

class StratumTools
{
	/**
	 *
	 * @param $folder
	 * @return unknown_type
	 */
	public static function getPlugins( $folder = 'Stratum' )
	{
		$database = JFactory::getDBO( );
		$folder = strtolower( $folder );

		//TODO: use latest joomla query
		$query = "
			SELECT 
				* 
			FROM 
				#__extensions 
			WHERE 
				`type` = 'plugin'
			AND 
				LOWER(`folder`) = '{$folder}'
			ORDER BY ordering ASC
		";

		$database->setQuery( $query );
		$data = $database->loadObjectList( );

		return $data;
	}

	/**
	 *
	 * @param $element
	 * @param $eventName
	 * @return unknown_type
	 */
	public static function hasEvent( $element, $eventName, $group )
	{
		$success = false;
		if( !$element || !is_object( $element ) )
		{
			return $success;
		}

		if( !$eventName || !is_string( $eventName ) )
		{
			return $success;
		}

		// Check if they have a particular event
		$import = JPluginHelper::importPlugin( strtolower( $group ), $element->element );
		$dispatcher = JDispatcher::getInstance( );
		$result = $dispatcher->trigger( $eventName, array( $element ) );
		if( in_array( true, $result, true ) )
		{
			$success = true;
		}
		return $success;
	}

}
