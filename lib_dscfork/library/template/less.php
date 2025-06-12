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

class StratumTemplateLESS
{
	function __construct( $parent )
	{

		if ( $parent->API->get( 'recompile_css', 0 ) == 1 )
		{
			// remove old Template CSS files
			jimport( 'joomla.filesystem.file' );
			JFile::delete( $parent->API->URLtemplatepath( ) . '/css/global.css' );
			JFile::delete( $parent->API->URLtemplatepath( ) . '/css/default.css' );
			JFile::delete( $parent->API->URLtemplatepath( ) . '/css/print.css' );
			JFile::delete( $parent->API->URLtemplatepath( ) . '/css/mail.css' );
			JFile::delete( $parent->API->URLtemplatepath( ) . '/css/error.css' );
			JFile::delete( $parent->API->URLtemplatepath( ) . '/css/offline.css' );
			JFile::delete( $parent->API->URLtemplatepath( ) . '/css/override.css' );

			// generate new Template CSS files
			try
			{
				// normal Template code
				$less = new StratumTemplateHelperLessc;
				$less->checkedCompile( $parent->API->URLtemplatepath( ) . '/less/global.less', $parent->API->URLtemplatepath( ) . '/css/global.css' );
				$less->checkedCompile( $parent->API->URLtemplatepath( ) . '/less/default.less', $parent->API->URLtemplatepath( ) . '/css/default.css' );
				$less->checkedCompile( $parent->API->URLtemplatepath( ) . '/less/print.less', $parent->API->URLtemplatepath( ) . '/css/print.css' );
				$less->checkedCompile( $parent->API->URLtemplatepath( ) . '/less/mail.less', $parent->API->URLtemplatepath( ) . '/css/mail.css' );
				
				// additional Template code
				$less->checkedCompile( $parent->API->URLtemplatepath( ) . '/less/error.less', $parent->API->URLtemplatepath( ) . '/css/error.css' );
				$less->checkedCompile( $parent->API->URLtemplatepath( ) . '/less/offline.less', $parent->API->URLtemplatepath( ) . '/css/offline.css' );
				$less->checkedCompile( $parent->API->URLtemplatepath( ) . '/less/override.less', $parent->API->URLtemplatepath( ) . '/css/override.css' );
			} catch (exception $ex)
			{
				exit( 'LESS Parser fatal error:<br />' . $ex->getMessage( ) );
			}
		}
	}

}
