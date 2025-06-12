<?php
/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library/model
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// Assuming StratumModel is autoloaded or in the same conceptual namespace path
// If StratumModel was namespaced, e.g., LibDscfork\Library\Model\StratumModel,
// a 'use LibDscfork\Library\Model\StratumModel;' would be needed if not in same namespace.

class StratumModelElement extends StratumModel
{
	public $cache_enabled = false;

	public $title_key = 'title';
	public $select_title_constant = 'LIB_STRATUM_SELECT_ITEM';
	public $select_constant = 'LIB_STRATUM_SELECT';
	public $clear_constant = 'LIB_STRATUM_CLEAR_SELECTION';

	public function __construct( $config = array() )
	{
		parent::__construct( $config );

		if ( !empty( $this->option ) )
		{
			$option = $this->option;
		} else
		{
			$r = null;

			if ( !preg_match( '/(.*)Model/i', get_class( $this ), $r ) )
			{
				throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}

			$option = 'com_' . strtolower( $r[1] );
		}

		$lang = Factory::getLanguage( );
		$lang->load( $option );
		$lang->load( $option, JPATH_ADMINISTRATOR );
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function fetchElement( $name, $value = '', $control_name = '', $js_extra = '', $fieldName = '' )
	{
		$doc = Factory::getApplication()->getDocument( );

		if ( empty( $fieldName ) )
		{
			$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;
		}

		if ( $value )
		{
			$table = $this->getTable( );
			$table->load( $value );
			$title_key = $this->title_key;
			$title = $table->$title_key;
		} else
		{
			$title = Text::_( $this->select_title_constant );
		}

		$close_window = "window.parent.SqueezeBox.close();";

		$js = "stratum.select" . $this->getName( ) . " = function(id, title, object) {
                        document.getElementById(object + '_id').value = id;
                        document.getElementById(object + '_name').value = title;
                        document.getElementById(object + '_name_hidden').value = title;
        $close_window
        $js_extra
                   }";
		$doc->addScriptDeclaration( $js );

		if ( !empty( $this->option ) )
		{
			$option = $this->option;
		} else
		{
			$r = null;

			if ( !preg_match( '/(.*)Model/i', get_class( $this ), $r ) )
			{
				throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
			}

			$option = 'com_' . strtolower( $r[1] );
		}
		$link = 'index.php?option=' . $option . '&view=' . $this->getName( ) . '&tmpl=component&object=' . $name;

		HTMLHelper::_( 'behavior.modal', 'a.modal' );
		$html = "\n" . '<input type="text" id="' . $name . '_name" value="' . htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' ) . '" disabled="disabled" />';
		$html .= '<a class="modal btn btn-primary" style="color : white; margin-left : 2px;" title="' . Text::_( $this->select_title_constant ) . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">' . Text::_( $this->select_constant ) . '</a>' . "\n";
		$html .= "\n" . '<input type="hidden" id="' . $name . '_id" name="' . $fieldName . '" value="' . $value . '" />';
		$html .= "\n" . '<input type="hidden" id="' . $name . '_name_hidden" name="' . $name . '_name_hidden" value="' . htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' ) . '" />';

		return $html;
	}

	/**
	 *
	 * @return
	 * @param object $name
	 * @param object $value[optional]
	 * @param object $node[optional]
	 * @param object $control_name[optional]
	 */
	function clearElement( $name, $value = '', $control_name = '' )
	{
		$doc = Factory::getApplication()->getDocument( );
		$fieldName = $control_name ? $control_name . '[' . $name . ']' : $name;

		$js = "
            stratum.reset" . $this->getName( ) . " = function(id, title, object) {
                document.getElementById(object + '_id').value = id;
                document.getElementById(object + '_name').value = title;
            }";
		$doc->addScriptDeclaration( $js );

		$html = '
                    <a href="javascript:void(0);" style="color : white;" class="btn btn-danger" onclick="stratum.reset' . $this->getName( ) . '( \'' . $value . '\', \'' . Text::_( $this->select_title_constant ) . '\', \'' . $name . '\' )">' . Text::_( $this->clear_constant ) . '
                    </a>
            ';

		return $html;
	}

}
