<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., plugin events, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
// TODO: J4/5 Consider if specific Joomla CMS classes should be imported via `use` statements here.
// use Joomla\CMS\Factory;
// use Joomla\CMS\Plugin\CMSPlugin; // JPlugin is an alias
// use Joomla\CMS\Filesystem\File;
// use Joomla\CMS\Language\Text;
// use Joomla\CMS\Uri\Uri; // For JURI if used more extensively
// use Joomla\CMS\HTML\HTMLHelper; // For JHTML if used more extensively
// use Joomla\CMS\Router\Route; // For JRoute if used
// use Joomla\CMS\Component\ComponentHelper; // For JComponentHelper if used

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
		// TODO: J4/5 Replace jimport with 'use' statement.
		//jimport( 'joomla.application.component.controller' );
		// TODO: J4/5 Replace jimport with 'use' statement.
		//jimport( 'joomla.application.component.model' );
		// TODO: J4/5 Replace jimport with 'use' statement.
		//jimport( 'joomla.application.component.view' );

		//TODO: Add the compatibilty (ie.StratumTableBase) in the future release of joomla 4?
		// Load the Base classes
		JLoader::register( 'StratumTable', JPATH_SITE . '/libraries/stratum/library/table.php' );
		JLoader::register( 'StratumController', JPATH_SITE . '/libraries/stratum/library/controller.php' );
		JLoader::register( 'StratumModel', JPATH_SITE . '/libraries/stratum/library/model.php' );
		JLoader::register( 'StratumView', JPATH_SITE . '/libraries/stratum/library/view.php' );

		if( !class_exists( 'Stratum' ) )
		{
			// TODO: J4/5 update JFile::exists() to use imported File class (e.g., File::exists()).
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
