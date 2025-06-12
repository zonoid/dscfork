<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library/social
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class StratumSocialGoogle extends StratumSocial
{

	function sharebutton( $url = NULL )
	{
		if ( empty( $url ) )
		{
			$url = JURI::getInstance( )->toString( );
		}

		$html = '<script src="https://apis.google.com/js/plusone.js"></script>
					<g:plus action="share"></g:plus>';
		return $html;
	}

	function customsharebutton( $url = NULL, $attribs = array() )
	{
		if ( empty( $url ) )
		{
			$url = JURI::getInstance( )->toString( );
		}

		$text = 'Google';
		if ( @$attibs['text'] )
		{
			$text = $attibs['text'];
		}

		if ( @$attibs['img'] )
		{
			$text = $attibs['img'];
		}

		$onclick = "javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";
		$html = '<a class="btn socialBtn socialbtnGoogle socialbtnGoogleShare" href="https://plus.google.com/share?url=' . $url . '" onclick="' . $onclick . '">' . $text . '</a>';

		return $html;
	}

}
