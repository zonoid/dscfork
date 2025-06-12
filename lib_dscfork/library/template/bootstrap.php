<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dscfork/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage library/template
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later *
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class StratumTemplateBootstrap
{

	function __construct( $parent )
	{
		if ( $parent->API->get( 'recompile_bootstrap', 0 ) == 1 )
		{

			$framework = $parent->API->get( 'cssframework', 0 );
			if ( strlen( $framework ) )
			{
				// remove old Bootstrap CSS files
				jimport( 'joomla.filesystem.file' );
				JFile::delete( $parent->API->URLtemplatepath( ) . '/css/base.css' );
				JFile::delete( $parent->API->URLtemplatepath( ) . '/css/responsive.css' );
				// generate new Bootstrap CSS files
				try
				{
					$less = new StratumTemplateHelperLessc;
					// normal Bootstrap code
					$less->checkedCompile( $parent->API->URLtemplatepath( ) . '/framework/' . $framework . '/less/bootstrap.less', $parent->API->URLtemplatepath( ) . '/css/base.css' );
					// responsive Bootstrap code
					$less->checkedCompile( $parent->API->URLtemplatepath( ) . '/framework/' . $framework . '/less/responsive.less', $parent->API->URLtemplatepath( ) . '/css/responsive.css' );
				} catch (exception $ex)
				{
					exit( 'LESS Parser fatal error:<br />' . $ex->getMessage( ) );
				}

			}

		}
	}

}

// EOF
