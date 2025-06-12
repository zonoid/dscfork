<?php
<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
// TODO: J4/5 Consider if specific Joomla CMS classes should be imported via `use` statements here.
// use Joomla\CMS\Language\Text;

/**
 * 	Fork of Dioscouri Library @see https://github.com/dioscouri/library
 *
 * 	@package	Dioscouri Fork Library
 *  @subpackage	library
 * 	@author 	Gerald R. Zalsos
 * 	@link 		http://www.klaraontheweb.com
 * 	@copyright 	Copyright (C) 2015 klaraontheweb.com All rights reserved.
 * 	@license 	Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class StratumCharts extends JObject
{

	/**
	 * renderGoogleChart function.
	 *
	 * @access public
	 * @param mixed $data
	 * @param string $title. (default: 'A Stratum Google Chart')
	 * @param string $type. (default: 'Column')
	 * @param int $width. (default: 900)
	 * @param int $height. (default: 250)
	 * @return void
	 */
	public static function renderGoogleChart( $data, $title = 'A Stratum Google Chart', $type = 'Column', $width = 800, $height = 250 )
	{
		$title = JText::_( $title );

		// Chart types
		switch ($type)
		{
			case 'Bar':
			case 'Bar2D':
				$type = 'bhs';
				break;
			case 'Line':
				$type = 'lc';
				break;
			default:
				$type = 'bvs';
				break;
		}

		$datastr = '';
		$labelstr = '';
		$max = 0;
		foreach ( $data as $obj )
		{
			$max = ($obj->value > $max) ? $obj->value : $max;
			$datastr .= strlen( $datastr ) ? ',' . $obj->value : $obj->value;
			$labelstr .= '|' . $obj->label;
		}

		$url = 'http://chart.apis.google.com/chart?chs=' . $width . 'x' . $height . '&cht=' . $type . '&chtt=' . $title . '&chxt=x,y&chxl=0:' . $labelstr . '&chxr=1,0,' . $max . '&chd=t:' . $datastr . '&chds=0,' . $max . '&chxs=0,,9&chbh=a';
		$chart = "<img src='$url' alt='$title' />";
		return $chart;
	}

}
