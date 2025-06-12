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
defined( '_JEXEC' ) or die( 'Restricted access' );

abstract class StratumElement extends JObject
{
	public $element = array( );
	public $name = null;
	// form field name
	public $id = null;
	// form field id
	public $value = null;
	// form field value
	public $asset = null;
	// component name by default

	public function __construct( $config = array() )
	{
		$this->setProperties( $config );

		if ( empty( $this->asset ) )
		{
			$this->asset = JFactory::getApplication( )->input->getCmd( 'option' );
		}
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	abstract public function fetchElement( $name, $value = '', $attribs = array() );
}
