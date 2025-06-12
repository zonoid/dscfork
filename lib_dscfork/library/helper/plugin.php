<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library/helper
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class StratumHelperPlugin extends StratumHelper
{
	/**
	 * Only returns plugins that have a specific event
	 *
	 * @param $eventName
	 * @param $folder
	 * @return array of JTable objects
	 */
	function getPluginsWithEvent( $eventName, $folder = 'Stratum' )
	{
		$return = array( );
		if ( $plugins = StratumHelperPlugin::getPlugins( $folder ) )
		{
			foreach ( $plugins as $plugin )
			{
				if ( StratumHelperPlugin::hasEvent( $plugin, $eventName ) )
				{
					$return[] = $plugin;
				}
			}
		}
		return $return;
	}

	/**
	 * Returns Array of active Plugins
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function getPlugins( $folder = 'Stratum' )
	{
		$folder = strtolower( $folder );
		$db = JFactory::getDBO( );

		$query = $db->getQuery( true );
		$query->select( $db->quoteName( '*' ) );
		$query->from( $db->quoteName( '#__extensions' ) );
		$query->where( $db->quoteName( 'enabled' ) . '=' . $db->quote( '1' ) );
		$query->where( 'LOWER(`folder`)  =' . $db->quote( $folder ) );

		$db->setQuery( $query );
		$data = $db->loadObjectList( );
		return $data;
	}

	/**
	 * Returns HTML
	 * @param mixed Boolean
	 * @param mixed Boolean
	 * @return array
	 */
	function & getPluginsContent( $event, $options, $method = 'vertical' )
	{
		$text = "";
		jimport( 'joomla.html.pane' );
		//TODO: convert to joomla 3 slider/tab

		if ( !$event )
		{
			return $text;
		}

		$args = array( );
		$dispatcher = &JDispatcher::getInstance( );
		$results = $dispatcher->trigger( $event, $options );

		if ( !count( $results ) > 0 )
		{
			return $text;
		}

		// grab content
		switch( strtolower($method) )
		{
			case "vertical":
				for ( $i = 0; $i < count( $results ); $i++ )
				{
					$result = $results[$i];
					$title = $result[1] ? JText::_( $result[1] ) : JText::_( 'Info' );
					$content = $result[0];

					// Vertical
					$text .= '<p>' . $content . '</p>';
				}
				break;
			case "tabs":
				break;
		}

		return $text;
	}

	/**
	 * Checks if a plugin has an event
	 *
	 * @param obj      $element    the plugin JTable object
	 * @param string   $eventName  the name of the event to test for
	 * @return unknown_type
	 */
	function hasEvent( $element, $eventName )
	{
		$success = false;
		if ( !$element || !is_object( $element ) )
		{
			return $success;
		}

		if ( !$eventName || !is_string( $eventName ) )
		{
			return $success;
		}

		// Check if they have a particular event
		$import = JPluginHelper::importPlugin( strtolower( 'Stratum' ), $element->element );
		$dispatcher = JDispatcher::getInstance( );
		$result = $dispatcher->trigger( $eventName, array( $element ) );
		if ( in_array( true, $result, true ) )
		{
			$success = true;
		}
		return $success;
	}

}
