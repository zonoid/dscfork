<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later *
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class StratumUrl
{
	/**
	 * Wrapper that adds the current Itemid to the URL
	 *
	 * @param	string $string The string to translate
	 *
	 */
	public static function _( $url, $text, $params='', $xhtml=true, $ssl=null, $addItemid='1' ) 
	{
		if ($addItemid == '1') { $url = StratumUrl::addItemid($url); }
		$return = "<a href='".JRoute::_($url, $xhtml, $ssl)."' ".addslashes($params)." >".$text."</a>";
		return $return;			
	}

	/**
	 * Wrapper that adds the current Itemid to the URL
	 *
	 * @param	string $string The string to translate
	 *
	 */
	public static function addItemid( $url ) 
	{
		global $Itemid;
		$return = $url;
		$return.= "&Itemid=".$Itemid;
		return $return;			
	}

	/**
	 * Displays a url in a lightbox
	 * 
	 * @param $url
	 * @param $text
	 * @param array options( 
	 * 				'width',
	 *				'height',
	 * 				'top',
	 * 				'left',
	 * 				'class',
	 * 				'update',
	 * 				'img'
	 * 				)
	 * @return popup html
	 */
	public static function popup( $url, $text, $options = array() ) 
	{
		$html = "";
		
		if (!empty($options['update']))
		{
		    JHTML::_('behavior.modal', 'a.modal', array('onClose'=>'\function(){stratum.update();}') );
		}
            else
		{
		    JHTML::_('behavior.modal');
		}

		// set the $handler_string based on the user's browser
        $handler_string = "{handler:'iframe',size:{x: window.innerWidth-80, y: window.innerHeight-80}, onShow:$('sbox-window').setStyles({'padding': 0})}";
	    $browser = Stratum::getClass( 'StratumBrowser', 'library.browser' );
        if ( $browser->getBrowser() == StratumBrowser::BROWSER_IE )
        {
            // if IE, use 
            $handler_string = "{handler:'iframe',size:{x:window.getSize().scrollSize.x-80, y: window.getSize().size.y-80}, onShow:$('sbox-window').setStyles({'padding': 0})}";            
        }
		
		$handler = (!empty($options['img']))
		  ? "{handler:'image'}"
		  : $handler_string;

		if (!empty($options['width']))
		{
			if (empty($options['height']))
			{
				$options['height'] = 480;
			}
			$handler = "{handler: 'iframe', size: {x: ".$options['width'].", y: ".$options['height']. "}}";
		}

		$id = (!empty($options['id'])) ? $options['id'] : '';
		$class = (!empty($options['class'])) ? $options['class'] : '';
		$linkclass = (!empty($options['linkclass'])) ? $options['linkclass'] : '';
		$linkclass = $linkclass . ' modal';
		$html	= "<a class=\"".$linkclass."\" href=\"$url\" rel=\"$handler\" >\n";
		$html 	.= "<span class=\"".$class."\" id=\"".$id."\" >\n";
        $html   .= "$text\n";
		$html 	.= "</span>\n";
		$html	.= "</a>\n";
		
		return $html;
	}

}