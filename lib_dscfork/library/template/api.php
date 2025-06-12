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

class StratumTemplateAPI extends JObject
{
	private $API;

	function __construct( $parentTpl )
	{
		$this->API = $parentTpl;
	}

	public function addCSS( $url, $type = 'text/css', $media = null )
	{
		$this->API->addStyleSheet( $url, $type, $media );
	}

	public function addJS( $url )
	{
		$this->API->addScript( $url );
	}

	public function addCSSRule( $code )
	{
		$this->API->addStyleDeclaration( $code );
	}

	public function addJSFragment( $code )
	{
		$this->API->addScriptDeclaration( $code );
	}

	public function get( $key, $default = null )
	{
		return $this->API->params->get( $key, $default );
	}

	public function modules( $rule )
	{
		return $this->API->countModules( $rule );
	}

	public function URLbase( )
	{
		return JURI::base( );
	}

	public function URLtemplate( )
	{
		return JURI::base( ) . "templates/" . $this->API->template;
	}

	public function URLpath( )
	{
		return JPATH_SITE;
	}

	public function URLtemplatepath( )
	{
		return $this->URLpath( ) . "/templates/" . $this->API->template;
	}

	public function getTemplateLayoutPath( $layout )
	{
		return $this->URLpath( ) . "/templates/" . $this->API->template . '/layouts/' . $layout;
	}

	public function templateName( )
	{
		return $this->API->template;
	}

	public function getPageName( )
	{
		$config = new JConfig( );
		return $config->sitename;
	}

	public function addFavicon( )
	{
		$icon = $this->URLtemplatepath( ) . '/images/ico/favicon.ico';
		return $this->API->addFavicon( $icon );
	}

}
