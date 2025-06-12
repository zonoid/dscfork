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

class StratumSocialFacebook extends StratumSocial
{

	function sharebutton( $url = NULL )
	{
		if ( empty( $url ) )
		{
			$url = JURI::getInstance( )->toString( );
		}

		//TODO: add language support
		$html = '<div class="fb_share"><a name="fb_share" type="box_count" share_url="$url"
     			 href="http://www.facebook.com/sharer.php">Share</a>
    			<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
			</div>';
		return $html;
	}

	function customsharebutton( $url = NULL, $attribs = array() )
	{
		if ( empty( $url ) )
		{
			$url = JURI::getInstance( )->toString( );
		}
		$text = 'Facebook';
		if ( @$attibs['text'] )
		{
			$text = $attibs['text'];
		}
		if ( @$attibs['img'] )
		{
			$text = $attibs['img'];
		}
		$onclick = "javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";
		$html = '<a class="btn socialBtn socialbtnFacebook socialbtnFacebookShare" onclick="' . $onclick . '" href="http://www.facebook.com/share.php?u=' . $url . '">' . $text . '</a>';
		return $html;
	}

}
