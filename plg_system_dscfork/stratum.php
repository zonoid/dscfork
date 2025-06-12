<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	plg_system_dscfork
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemStratum extends JPlugin
{
	function onAfterInitialise( )
	{
		// Import Joomla! classes
		jimport( 'joomla.application.component.controller' );
		jimport( 'joomla.application.component.model' );
		jimport( 'joomla.application.component.view' );

		//TODO: Add the compatibilty (ie.StratumTableBase) in the future release of joomla 4?
		// Load the Base classes
		JLoader::register( 'StratumTable', JPATH_SITE . '/libraries/stratum/library/table.php' );
		JLoader::register( 'StratumController', JPATH_SITE . '/libraries/stratum/library/controller.php' );
		JLoader::register( 'StratumModel', JPATH_SITE . '/libraries/stratum/library/model.php' );
		JLoader::register( 'StratumView', JPATH_SITE . '/libraries/stratum/library/view.php' );

		if( !class_exists( 'Stratum' ) )
		{
			if( !JFile::exists( JPATH_SITE . '/libraries/stratum/stratum.php' ) )
			{
				return false;
			}
			require_once JPATH_SITE . '/libraries/stratum/stratum.php';
		}

		$language = JFactory::getLanguage();
        $language -> load('lib_stratum', JPATH_ROOT, '', true);
		
		return Stratum::loadLibrary( );
	}

}
