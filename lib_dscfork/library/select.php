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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

if (JFile::exists(JPATH_SITE.'/libraries/cms/html/select.php')) {
    require_once( JPATH_SITE.'/libraries/cms/html/select.php' );
} else {
    require_once( JPATH_SITE.'/libraries/joomla/html/select.php' );
}

class StratumSelect extends JHTMLSelect
{
    /**
    * Generates a yes/no radio list with the arguments in a consistent order
    *
    * @param string The value of the HTML name attribute
    * @param string Additional HTML attributes for the <select> tag
    * @param mixed The key that is selected
    * @returns string HTML for the radio list
    */
    public static function booleanlist( $selected, $name='', $attribs = null, $yes = 'yes', $no = 'no', $id = false )
    {
        return parent::booleanlist( $name, $attribs, $selected, $yes, $no, $id );
    }
    
	/**
	* Generates a yes/no select list
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	public static function booleans( $selected, $name = 'filter_enabled', $attribs = array('class' => 'chzn-single chzn-single-with-drop'), $idtag = null, $allowAny = false, $title='Select State', $yes = 'Enabled', $no = 'Disabled' )
	{
	    $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -" );
		}

		$list[] = JHTML::_('select.option',  '0', JText::_( $no ) );
		$list[] = JHTML::_('select.option',  '1', JText::_( $yes ) );

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}

	/**
	* Generates range list
	*
	* @param string The value of the HTML name attribute
	* @param string Additional HTML attributes for the <select> tag
	* @param mixed The key that is selected
	* @returns string HTML for the radio list
	*/
	public static function range( $selected, $name = 'filter_range', $attribs = array('class' => 'chzn-single chzn-single-with-drop'), $idtag = null, $allowAny = false, $title = 'Select Range' )
	{
	    $list = array();
		if($allowAny) {
			$list[] =  self::option('', "- ".JText::_( $title )." -" );
		}

		//TODO: ADD TRANSLATION
		$list[] = JHTML::_('select.option',  'today', JText::_( "Today" ) );
		$list[] = JHTML::_('select.option',  'yesterday', JText::_( "Yesterday" ) );
		$list[] = JHTML::_('select.option',  'last_seven', JText::_( "Last Seven Days" ) );
		$list[] = JHTML::_('select.option',  'last_thirty', JText::_( "Last Thirty Days" ) );
		$list[] = JHTML::_('select.option',  'ytd', JText::_( "Year to Date" ) );

		return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
	}
	
    /**
    * Generates range list
    *
    * @param string The value of the HTML name attribute
    * @param string Additional HTML attributes for the <select> tag
    * @param mixed The key that is selected
    * @returns string HTML for the radio list
    */
    public static function reportrange( $selected, $name = 'filter_range', $attribs = array('class' => 'chzn-single chzn-single-with-drop'), $idtag = null, $allowAny = false, $title = 'Select Range' )
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( $title )." -" );
        }

		//TODO: ADD TRANSLATION
        $list[] = JHTML::_('select.option',  'custom', JText::_( "Custom" ) );
        $list[] = JHTML::_('select.option',  'yesterday', JText::_( "Yesterday" ) );
        $list[] = JHTML::_('select.option',  'last_week', JText::_( "Last Week" ) );
        $list[] = JHTML::_('select.option',  'last_month', JText::_( "Last Month" ) );
        $list[] = JHTML::_('select.option',  'ytd', JText::_( "Year to Date" ) );
        $list[] = JHTML::_('select.option',  'all', JText::_( "All Time" ) );
        
        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
    }

    /**
    * Generates a Period Unit Select List for recurring payments
    *
    * @param string The value of the HTML name attribute
    * @param string Additional HTML attributes for the <select> tag
    * @param mixed The key that is selected
    * @returns string HTML for the radio list
    */
    public static function periodUnit( $selected, $name = 'filter_periodunit', $attribs = array('class' => 'chzn-single chzn-single-with-drop'), $idtag = null, $allowAny = false, $title='Select Period Unit' )
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( $title )." -" );
        }

        $list[] = JHTML::_('select.option',  'D', JText::_( "Day" ) );
        $list[] = JHTML::_('select.option',  'W', JText::_( "Week" ) );
        $list[] = JHTML::_('select.option',  'M', JText::_( "Month" ) );
        $list[] = JHTML::_('select.option',  'Y', JText::_( "Year" ) );
        
        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
    }
	
	/**
     * A boolean radiolist that uses bootstrap
     *
     * @param unknown_type $name
     * @param unknown_type $attribs
     * @param unknown_type $selected
     * @param unknown_type $yes
     * @param unknown_type $no
     * @param unknown_type $id
     * @return string
     */
	public static function btbooleanlist($name, $attribs = null, $selected = null, $yes = 'JYES', $no = 'JNO', $id = false)
	{
	//	JHTML::_('script', 'stratum/bootstrapped-advanced-ui.js', false, true); //TODO: improve js
	    JHTML::_('stylesheet', 'stratum/bootstrapped-advanced-ui.css', array(), true);
	    $arr = array(JHtml::_('select.option', '0', JText::_($no)), JHtml::_('select.option', '1', JText::_($yes)));
	    $html = '';
	    //$html .= '<div class="control-group"><div class="controls">';
		$html .= '<fieldset id="'.$name.'" class="radio btn-group">';
	    $html .=  StratumSelect::btradiolist( $arr, $name, $attribs, 'value', 'text', (int) $selected, $id);
	    $html .= '</fieldset>';
	   // $html .= '</div></div>';
	    return $html;
	}

	/**
	 * A standard radiolist that uses bootstrap
	 *
	 * @param unknown_type $data
	 * @param unknown_type $name
	 * @param unknown_type $attribs
	 * @param unknown_type $optKey
	 * @param unknown_type $optText
	 * @param unknown_type $selected
	 * @param unknown_type $idtag
	 * @param unknown_type $translate
	 * @return string
	 */
	public static function btradiolist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
	{
	    reset($data);
	    $html = '';

	    if (is_array($attribs))
	    {
	        $attribs = JArrayHelper::__toString($attribs);
	    }

	    $id_text = $idtag ? $idtag : $name;

	    foreach ($data as $obj)
	    {
	        $k = $obj->$optKey;
	        $t = $translate ? JText::_($obj->$optText) : $obj->$optText;
	        $id = (isset($obj->id) ? $obj->id : null);

	        $extra = '';
	        $extra .= $id ? ' id="' . $obj->id . '"' : '';
	        if (is_array($selected))
	        {
	            foreach ($selected as $val)
	            {
	                $k2 = is_object($val) ? $val->$optKey : $val;
	                if ($k == $k2)
	                {
	                    $extra .= ' selected="selected"';
	                    break;
	                }
	            }
	        }
	        else
	        {
	            $extra .= ((string) $k == (string) $selected ? ' checked="checked"' : '');
	        }

	        $active ='';
	        if(!empty($k)) {
	            $active = 'active';
	        }

	        $html .= "\n\t" . '<input type="radio" name="' . $name . '"' . ' id="' . $id_text . $k . '" value="' . $k . '"' . ' ' . $extra . ' '
	        . $attribs . '/>' . "\n\t" . '<label for="' . $id_text . $k . '"' . ' id="' . $id_text . $k . '-lbl" class="btn">' . $t
	        . '</label>';
	    }

	    $html .= "\n";

	    return $html;
	}

}
