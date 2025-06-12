<?php
<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
// TODO: J4/5 Consider if specific Joomla CMS classes should be imported via `use` statements here.
// use Joomla\CMS\Factory;
// use Joomla\CMS\Event\Dispatcher;
// use Joomla\CMS\Table\Table;
// use Joomla\CMS\Plugin\PluginHelper;
// use Joomla\Registry\Registry;

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

class StratumArticle extends JObject
{
	/**
	 * Takes a simple description an formats it like an article
	 * to trick payment plugins into acting on it
	 *
	 * @param string $text
	 * @return string HTML
	 */
	public static function fromString( $text )
	{
		$app = JFactory::getApplication( );
		$params = $app->getParams( 'com_content' );

		$dispatcher = JDispatcher::getInstance( );

		$article = JTable::getInstance( 'content' );
		$article->text = $text;

		$limitstart = 0;

		/*
		 * Process the prepare content plugins
		 */
		// TODO: J4/5 Replace JPluginHelper::importPlugin with event dispatcher or service locator for plugins.
		// JPluginHelper::importPlugin( 'content' );
		$results = $dispatcher->trigger( 'onPrepareContent', array( &$article, &$params, $limitstart ) );

		/*
		 * Handle display events
		 */
		$article->event = new stdClass( );

		// TODO Since there is no title, do we include this event?
		$results = $dispatcher->trigger( 'onAfterDisplayTitle', array( $article, &$params, $limitstart ) );
		$article->event->afterDisplayTitle = trim( implode( "\n", $results ) );

		$results = $dispatcher->trigger( 'onBeforeDisplayContent', array( &$article, &$params, $limitstart ) );
		$article->event->beforeDisplayContent = trim( implode( "\n", $results ) );

		$results = $dispatcher->trigger( 'onAfterDisplayContent', array( &$article, &$params, $limitstart ) );
		$article->event->afterDisplayContent = trim( implode( "\n", $results ) );

		// collect $html
		$html = '';
		$html .= $article->event->afterDisplayTitle;
		$html .= $article->event->beforeDisplayContent;
		$html .= $article->text;
		$html .= $article->event->afterDisplayContent;

		return $html;

	}

	/**
	 * Takes Joomla article id to display the said article
	 *
	 * @param int $articleid
	 * @return string HTML
	 */
	public static function display( $articleid )
	{
		$html = '';

		$app = JFactory::getApplication( );

		$dispatcher = JDispatcher::getInstance( );

		$article = JTable::getInstance( 'content' );
		$article->load( $articleid );

		// Return html if the load fails
		if ( !$article->id )
			return $html;

		$article->text = $article->introtext . chr( 13 ) . chr( 13 ) . $article->fulltext;

		$limitstart = $app->input->getInt( 'limitstart', 0 );
		$params = $app->getParams( 'com_content' );

		$aparams = $article->attribs;
		$params->merge( $aparams );

		// merge isn't overwriting the global component params, so using this
		$article_params = new JRegistry( $article->attribs );

		// Fire Content plugins on the article so they change their sample
		/*
		 * Process the prepare content plugins
		 */
		// TODO: J4/5 Replace JPluginHelper::importPlugin with event dispatcher or service locator for plugins.
		// JPluginHelper::importPlugin( 'content' );
		$results = $dispatcher->trigger( 'onPrepareContent', array( &$article, &$params, $limitstart ) );

		/*
		 * Handle display events
		 */
		$article->event = new stdClass( );
		$results = $dispatcher->trigger( 'onAfterDisplayTitle', array( $article, &$params, $limitstart ) );
		$article->event->afterDisplayTitle = trim( implode( "\n", $results ) );

		$results = $dispatcher->trigger( 'onBeforeDisplayContent', array( &$article, &$params, $limitstart ) );
		$article->event->beforeDisplayContent = trim( implode( "\n", $results ) );

		$results = $dispatcher->trigger( 'onAfterDisplayContent', array( &$article, &$params, $limitstart ) );
		$article->event->afterDisplayContent = trim( implode( "\n", $results ) );

		// Use param for displaying article title
		$show_title = $article_params->get( 'show_title', $params->get( 'show_title' ) );
		if ( $show_title )
		{
			$html .= "<h3>{$article->title}</h3>";
		}
		$html .= $article->event->afterDisplayTitle;
		$html .= $article->event->beforeDisplayContent;
		$html .= $article->text;
		$html .= $article->event->afterDisplayContent;

		return $html;
	}

}
