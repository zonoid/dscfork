<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library/helper
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class StratumHelperString extends StratumHelper
{
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $text
	 * @return unknown_type
	 */
	public function truncateString( &$text, $length = '200', $suffix = '...' )
	{
		if ( empty( $text ) )
		{
			return $text;
		}

		$allowed_tags = "";
		// '<p><i><em>';

		$text = $this->stripArgumentFromTags( $text );
		$text = strip_tags( $text, $allowed_tags );

		$strlen = strlen( $text );
		if ( $length >= $strlen )
		{
			$length = $strlen;
		}

		$int = strpos( $text, ' ', $length );
		if ( $int < $length )
		{
			$int = $length;
		}
		$text = substr( $text, 0, $int );
		if ( !empty( $text ) && ($strlen > $int) )
		{
			$text .= $suffix;
		}

		//$this->closeTags( $text, "<p>", "</p>" );
		//$this->closeTags( $text, "<em>", "</em>" );

		return $text;
	}

	/**
	 *
	 * Enter description here ...
	 * @param $text
	 * @param $tag_open
	 * @param $tag_close
	 * @return unknown_type
	 */
	public function closeTags( &$text, $tag_open, $tag_close )
	{
		$p = substr_count( $text, $tag_open );
		$p_close = substr_count( $text, $tag_close );

		if ( $p > $p_close )
		{
			$diff = $p - $p_close;
			$text .= str_repeat( $tag_close, $diff );
		}

		if ( $p < $p_close )
		{
			$diff = $p_close - $p;
			$text = str_repeat( $tag_open, $diff ) . $text;
		}

		return $text;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $htmlString
	 * @return string
	 */
	public function stripArgumentFromTags( $htmlString )
	{
		$regEx = '/([^<]*<\s*[a-z](?:[0-9]|[a-z]{0,9}))(?:(?:\s*[a-z\-]{2,14}\s*=\s*(?:"[^"]*"|\'[^\']*\'))*)(\s*\/?>[^<]*)/i';
		// match any start tag

		$chunks = preg_split( $regEx, $htmlString, -1, PREG_SPLIT_DELIM_CAPTURE );
		$chunkCount = count( $chunks );

		$strippedString = '';
		for ( $n = 0; $n < $chunkCount; $n++ )
		{
			$strippedString .= $chunks[$n];
		}

		return $strippedString;
	}

}
