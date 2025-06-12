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

// Assuming DSCForkTable is autoloaded or required elsewhere.
// If not, a require_once might be needed here, e.g.:
// require_once dirname(__FILE__) . '/../table.php'; // Adjust path as necessary
use LibDscfork\Library\Table\DSCForkTable; // Placeholder if namespaced, adjust as needed

class StratumTableContent extends DSCForkTable
{
	protected $_tbl = '#__content'; // Table name set directly
	protected $_tbl_key = 'id';  // Primary key set directly

	public function __construct(&$db)
	{
		parent::__construct($db);
		$this->set('_suffix', 'content'); // Keep if _suffix is used
	}

}
