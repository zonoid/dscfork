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

// If StratumModel is not autoloaded, a require_once would be needed here, e.g.:
// require_once JPATH_LIBRARIES . '/lib_dscfork/library/model.php';
// If StratumModel is namespaced, e.g., LibDscfork\Library\Model\StratumModel:
// use LibDscfork\Library\Model\StratumModel;

use Joomla\CMS\Table\Table;
// Factory might not be needed if getDbo() is used from parent.

class SampleModelDashboard extends StratumModel
{
	function getTable( $name = 'Config', $prefix = 'SampleTable', $options = array() ) // Default to Config table
	{
		// The parent StratumModel::getTable() is already refactored.
		// This override is to specify a different table name ('Config') than what
		// the parent would default to ('Dashboard' based on this model's name).
		// It also ensures the correct prefix 'SampleTable' is used.

		// It's assumed that SampleTableConfig (which 'Config', 'SampleTable' resolves to)
		// is either autoloadable (e.g., via PSR-4 in lib_dscfork/component/table/Config.php or similar)
		// or its path is added via Table::addIncludePath() elsewhere, e.g., in a component dispatcher.
		// Forcing a path here like JPATH_LIBRARIES . '/lib_dscfork/component/table' might be too specific
		// if this model is meant to be generic, but could be a fallback.
		// Table::addIncludePath(JPATH_LIBRARIES . '/lib_dscfork/component/table'); // Example

		$options['dbo'] = $this->getDbo();
		return Table::getInstance( $name, $prefix, $options );
	}

}
