<?php
<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
// TODO: J4/5 Consider if specific Joomla CMS classes should be imported via `use` statements here.
// use Joomla\CMS\Factory;
// use Joomla\CMS\User\UserHelper;
// use Joomla\CMS\Language\Text;
// use Joomla\CMS\Router\Route;
// use Joomla\CMS\Uri\Uri;

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
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class StratumAcl
{

	/**
	 * Checks if the current user, or userID passed to function is an administrator
	 *
	 * @param int $userid 				- Joomla user id
	 * @param array $admin_groups 		- Joomla admin groups
	 * @param boolean $group_ids_passed	- if group_ids_passed = true then the admin_groups is an array of groupids and not their names
	 * @return boolean
	 */

	public static function isAdmin( $userid = NULL, $admin_groups = array("7", "8"), $group_ids_passed = true )
	{
		// TODO: J4/5 Replace jimport with 'use' statement.
		//jimport( 'joomla.user.helper' );
		$user = JFactory::getUser( $userid );
		$groups = UserHelper::getUserGroups( $user->id );

		if ( $group_ids_passed )
		{
			foreach ( $groups as $temp )
			{
				if ( in_array( $temp, $admin_groups ) )
					return true;
			}
		} else
		{
			foreach ( $admin_groups as $temp )
			{
				if ( !empty( $groups[$temp] ) )
					return true;
			}
		}

		return false;
	}

	/**
	 * Returns a list of users that should be administrators
	 * optional only return the query instead of the  object, so you can get arrays or objects or whatever.
	 *
	 * @param string|NULL $returnQuery
	 * @param boolean $sendEmail
	 * @return string|array
	 */

	public static function getAdminList( $returnQuery = NULL, $sendEmail = false )
	{
		//TODO should we be able to pass group_ids?
		//TODO use joomla query chaining
		$query = "
			SELECT
				u.*
			FROM
				#__users AS u
				INNER JOIN #__user_usergroup_map AS ug ON u.id = ug.user_id
			WHERE u.block = '0'
				AND ug.group_id = '8'
			";

		if ( $sendEmail )
			$query .= " AND u.sendEmail = '1' ";

		if ( $returnQuery != NULL )
			return $query;

		$database = JFactory::getDbo( );
		$database->setQuery( $query );
		$users = $database->loadObjectList( );

		return $users;
	}

	/**
	 * Add user to a group
	 *
	 * @param int $user_id - Joomla user id
	 * @param int $group_id - joomla group id
	 * @param boolean $only - if you want the user to in ONLY  the group you are adding  set only to true
	 * @return void
	 */

	public function addGroup( $user_id, $group_id, $only = false )
	{
		$user = JUser::getInstance( $user_id );

		if ( $only )
		{
			foreach ( $user->groups as $group )
			{
				unset( $user->groups[$group] );
			}

		}
		$user->groups[] = $group_id;

		// Bind the data.
		$user->bind( $user->groups );
		$user->save( );
	}

	/**
	 * Checks if a user is logged in and if not it redirections to login if not
	 *
	 * This is very simple on purpose you just do StratumAcl::validateUser(); at anytime and it  check for a valid user ID and redirect them to login. redirect back once logged in.
	 *
	 * IF YOU WANT REAL ACL YOU SHOULD USE JOOMLAS canAccess methods
	 *
	 * @param string $msg
	 *
	 */
	public static function validateUser( $msg = '' )
	{
		if ( empty( $msg ) )
			$msg = JText::_( 'LIB_STRATUM_YOU_MUST_LOGIN_FIRST' );

		$userId = JFactory::getUser( )->get( 'id' );
		if ( !$userId )
		{
			$app = JFactory::getApplication( );
			$return = JFactory::getURI( )->toString( );
			$url = 'index.php?option=com_users&view=login';
			$url .= '&return=' . base64_encode( $return );
			$app->redirect( $url, $msg );
			return false;
		}
		return $userId;
	}

}
