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

class StratumCountry extends JObject
{
	function getList( )
	{
		static $items;

		if ( !is_array( $items ) )
		{
			$items[0] = new JObject( );
			$items[0]->id = '0';
			$items[0]->title = 'Not Applicable';
		}
		return $items;
	}

}
