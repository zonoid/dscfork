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

class SampleTableTools extends SampleTable
{
	/**
	 * Could this be abstracted into the base?
	 *
	 * @param $db
	 * @return unknown_type
	 */
	function SampleTableTools( &$db )
	{
		// Joomla! 1.6+ code here
		$tbl_key = 'extension_id';
		$tbl_suffix = 'extensions';

		$this->set( '_suffix', $tbl_suffix );
		$name = "sample";

		parent::__construct( "#__{$tbl_suffix}", $tbl_key, $db );
	}

	function check( )
	{
		return true;
	}

}
