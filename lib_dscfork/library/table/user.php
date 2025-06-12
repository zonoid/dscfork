<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library/table
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class StratumTableUser extends StratumTable
{
	function StratumTableUser( &$db )
	{
		$tbl_key = 'id';
		$tbl_suffix = 'users';
		$this->set( '_suffix', $tbl_suffix );

		parent::__construct( "#__{$tbl_suffix}", $tbl_key, $db );
	}

}
