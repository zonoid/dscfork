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

class StratumSocialBitly extends StratumSocial
{
	/**
	 *
	 * @param unknown_type $url
	 * @param unknown_type $login
	 * @param unknown_type $appkey
	 * @param unknown_type $format
	 */
	function get_bitly_short_url( $url, $login, $appkey, $format = 'txt' )
	{
		$connectURL = 'http://api.bit.ly/v3/shorten?login=' . $login . '&apiKey=' . $appkey . '&uri=' . urlencode( $url ) . '&format=' . $format;
		return $this->curl_get_result( $connectURL );
	}

	/**
	 *
	 * @param unknown_type $url
	 * @param unknown_type $login
	 * @param unknown_type $appkey
	 * @param unknown_type $format
	 */
	function get_bitly_long_url( $url, $login, $appkey, $format = 'txt' )
	{
		$connectURL = 'http://api.bit.ly/v3/expand?login=' . $login . '&apiKey=' . $appkey . '&shortUrl=' . urlencode( $url ) . '&format=' . $format;
		return $this->curl_get_result( $connectURL );
	}

	/**
	 *
	 * @param unknown_type $url
	 * @return unknown
	 */
	function curl_get_result( $url )
	{
		//TODO: use Joomla JHttp
		$ch = curl_init( );
		$timeout = 5;
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		$data = curl_exec( $ch );
		curl_close( $ch );
		return $data;
	}

}
