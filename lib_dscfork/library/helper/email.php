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

class StratumHelperEmail extends StratumHelper
{
	/**
	 * Protected! Use getInstance()
	 */
	protected function StratumHelperEmail( )
	{
		parent::__construct( );
		$this->use_html = true;
	}

	/**
	 * Prepares and sends the email
	 *
	 * @param unknown_type $from
	 * @param unknown_type $fromname
	 * @param unknown_type $recipient
	 * @param unknown_type $subject
	 * @param unknown_type $body
	 * @param unknown_type $actions
	 * @param unknown_type $mode
	 * @param unknown_type $cc
	 * @param unknown_type $bcc
	 * @param unknown_type $attachment
	 * @param unknown_type $replyto
	 * @param unknown_type $replytoname
	 * @return unknown_type
	 */
	public function sendMail( $from, $fromname, $recipient, $subject, $body, $actions = NULL, $mode = NULL, $cc = NULL, $bcc = NULL, $attachment = NULL, $replyto = NULL, $replytoname = NULL )
	{
		$success = false;
		$mailer = JFactory::getMailer( );
		$mailer->addRecipient( $recipient );
		$mailer->setSubject( $subject );

		// check user mail format type, default html
		$mailer->IsHTML( $this->use_html );
		$body = htmlspecialchars_decode( $body );
		$mailer->setBody( $body );

		$sender = array( $from, $fromname );
		$mailer->setSender( $sender );

		$sent = $mailer->send( );
		if ( $sent == '1' )
		{
			$success = true;
		}

		return $success;
	}

	/**
	 * Gets all targets for system emails
	 *
	 * return array of objects
	 */
	public function getSystemEmailRecipients( )
	{
		$db = &JFactory::getDBO( );
		$query = "
            SELECT tbl.email
            FROM #__users AS tbl
            WHERE tbl.sendEmail = '1';
        ";
		$db->setQuery( $query );
		$items = $db->loadObjectList( );
		if ( empty( $items ) )
		{
			return array( );
		}
		return $items;
	}

	/**
	 * Creates the placeholder array with the default site values
	 *
	 * @return unknown_type
	 */
	public function getPlaceholderDefaults( )
	{
		$mainframe = JFactory::getApplication( );
		$config = Stratum::getApp();
		$site_name = $config->get( 'sitename', $mainframe->getCfg( 'sitename' ) );
		$site_url = $config->get( 'siteurl', JURI::root( ) );
		$user_name = JText::_( $config->get( 'default_email_user_name', 'Valued Customer' ) );

		// default placeholders
		$placeholders = array( 'site.name' => $site_name, 'site.url' => $site_url, 'user.name' => $user_name );

		return $placeholders;
	}

	/**
	 * Replaces placeholders with their values
	 *
	 * @param string $text
	 * @param array $placeholders
	 * @return string
	 * @access public
	 */
	public function replacePlaceholders( $text, $placeholders )
	{
		$plPattern = '{%key%}';

		$plKeys = array( );
		$plValues = array( );

		foreach ( $placeholders as $placeholder => $value )
		{
			$plKeys[] = str_replace( 'key', $placeholder, $plPattern );
			$plValues[] = $value;
		}

		$text = str_replace( $plKeys, $plValues, $text );
		return $text;
	}

}
