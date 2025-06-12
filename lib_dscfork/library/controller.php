<?php
<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).

use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Exception\InvalidTokenException;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Event\Event;
use Joomla\CMS\Log\Log;

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

class StratumController extends BaseController
{
	/**
	 * default view
	 */
	public $default_view = 'dashboard';
	public $show_footer = true;

	/**
	 * @var array() instances of Models to be used by the controller
	 */
	public $_models = array( );

	/**
	 * string url to perform a redirect with. Useful for child classes.
	 */
	protected $redirect;

	var $_name = NULL;
	var $_Pluginname = NULL;

	/**
	 * constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );

		$com = $this->input->getCmd( 'option' );
		if ( !empty( $config['com'] ) )
		{
			$com = $config['com'];
		}

		//do we really need to get the whole app to get the name or should we strip it from the option??
		$app = Stratum::getApp( );
		$this->_name = $app->getName( );
		$this->_Pluginname = ucfirst( $this->_name );

		$this->set( 'com', $com );
		$this->set( 'suffix', $this->get( 'default_view' ) );

		$this->list_url = "index.php?option=" . $this->get( 'com' ) . "&view=" . $this->get( 'suffix' );

		// Register Extra tasks
		$this->registerTask( 'list', 'display' );
		$this->registerTask( 'close', 'cancel' );
	}

	/**
	 * 	display the view
	 */
	function display( $cachable = false, $urlparams = array() )
	{
		$app = Factory::getApplication( );

		// this sets the default view
		$this->input->set( 'view', $this->input->getCmd( 'view', $this->get( 'default_view' ) ) );

		$document = Factory::getApplication()->getDocument( );

		$viewType = $document->getType( );
		// Use default_view if getName() was providing that context, or adjust if $this->_name is more appropriate.
		// Based on analysis, default_view is better here.
		$viewName = $this->input->getCmd('view', $this->default_view);

		$viewLayout = $this->input->getCmd( 'layout', 'default' );

		$view = $this->getView( $viewName, $viewType, '', array( 'base_path' => $this->_basePath ) );

		// Get/Create the model
		if ( $model = $this->getModel( $viewName ) )
		{
			// controller sets the model's state - this is why we override parent::display()
			$this->_setModelState( );
			// Push the model into the view (as default)
			$view->setModel( $model, true );
		}

		// Set the layout
		$view->setLayout( $viewLayout );

		// Set the task in the view, so the view knows it is a valid task
		if ( in_array( $this->getTask( ), array_keys( $this->getTaskMap( ) ) ) )
		{
			$view->setTask( $this->getDoTask( ) );
		}

		$site = '';
		if ( $app->isAdmin( ) )
		{
			$site = 'Admin';
		}

		// Dispatch the onBeforeDisplay event
		$eventNameBeforeDisplay = 'onBeforeDisplay' . $site . 'Component' . $this->_Pluginname;
		$eventBeforeDisplay = new Event($eventNameBeforeDisplay, ['subject' => $this]);
		Factory::getDispatcher()->dispatch($eventNameBeforeDisplay, $eventBeforeDisplay);

		// Display the view
		if ( $cachable && $viewType != 'feed' )
		{
			$option = $this->get( 'com' );
			$cache = Factory::getCache( $option, 'view' );
			$cache->get( $view, 'display' );
		} else
		{
			$view->display( );
		}

		// Dispatch the onAfterDisplay event
		$eventNameAfterDisplay = 'onAfterDisplay' . $site . 'Component' . $this->_Pluginname;
		$eventAfterDisplay = new Event($eventNameAfterDisplay, ['subject' => $this]);
		Factory::getDispatcher()->dispatch($eventNameAfterDisplay, $eventAfterDisplay);

		$this->footer( );

		return $this;
	}

	/**
	 * Gets the view's namespace for state variables
	 * @return string
	 */
	function getNamespace( )
	{
		$app = Factory::getApplication( );
		$model = $this->getModel( $this->get( 'suffix' ) );
		$ns = $app->getName( ) . '::' . 'com.' . $this->get( 'com' ) . '.model.' . $model->getTable( )->get( '_suffix' );
		return $ns;
	}

	/**
	 * Sets the model's default state based on values in the request
	 *
	 * @return array()
	 */
	function _setModelState( )
	{
		$app = Factory::getApplication( );
		$model = $this->getModel( $this->get( 'suffix' ) );
		$ns = $this->getNamespace( );

		$state = array( );

		$state['limit'] = $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg( 'list_limit' ), 'int' );
		$state['limitstart'] = $app->getUserStateFromRequest( $ns . 'limitstart', 'limitstart', 0, 'int' );
		$state['order'] = $app->getUserStateFromRequest( $ns . '.filter_order', 'filter_order', 'tbl.' . $model->getTable( )->getKeyName( ), 'cmd' );
		$state['direction'] = $app->getUserStateFromRequest( $ns . '.filter_direction', 'filter_direction', 'ASC', 'word' );
		$state['filter'] = $app->getUserStateFromRequest( $ns . '.filter', 'filter', '', 'string' );
		$state['filter_enabled'] = $app->getUserStateFromRequest( $ns . 'filter_enabled', 'filter_enabled', '', '' );
		$state['filter_id_from'] = $app->getUserStateFromRequest( $ns . 'filter_id_from', 'filter_id_from', '', '' );
		$state['filter_id_to'] = $app->getUserStateFromRequest( $ns . 'filter_id_to', 'filter_id_to', '', '' );
		$state['filter_name'] = $app->getUserStateFromRequest( $ns . 'filter_name', 'filter_name', '', '' );
		$state['id'] = $this->input->post->getInt( 'id', $this->input->getInt( 'id' ) );

		// TODO santize the filter
		// $state['filter']   	=

		foreach ( @$state as $key => $value )
		{
			$model->setState( $key, $value );
		}
		return $state;
	}

	/**
	 * Gets the model
	 * We override parent::getModel because parent::getModel always creates a new Model instance
	 *
	 */
	function getModel( $name = '', $prefix = '', $config = array() )
	{
		if ( empty( $name ) )
		{
			// Use default_view as per analysis for model name default
			$name = $this->default_view;
		}

		if ( empty( $prefix ) )
		{
			// Use _name (component name) for prefix as per analysis
			$prefix = $this->_name . 'Model';
		}

		$fullname = strtolower( $prefix . $name );
		if ( empty( $this->_models[$fullname] ) )
		{
			if ( $model = $this->_createModel( $name, $prefix, $config ) )
			{
				// task is a reserved state
				$model->setState( 'task', @$this->_task );

				// Lets get the application object and set menu information if its available
				$app = Factory::getApplication( );
				$menu = $app->getMenu( );
				if ( is_object( $menu ) )
				{
					if ( $item = $menu->getActive( ) )
					{
						$params = $menu->getParams( $item->id );
						// Set Default State Data
						$model->setState( 'parameters.menu', $params );
					}
				}
			} else
			{
				$model = new StratumModel( );
			}
			$this->_models[$fullname] = $model;
		}

		return $this->_models[$fullname];
	}

	/**
	 * Gets the available tasks in the controller.
	 *
	 * @return  array  Array[i] of task names.
	 * @since   2.0
	 */
	public function getTaskMap( )
	{
		return $this->taskMap;
	}

	/**
	 * Gets the available tasks in the controller.
	 *
	 * @return  array  Array[i] of task names.
	 * @since   2.0
	 */
	public function getDoTask( )
	{
		return $this->doTask;
	}

	/**
	 * Sets the tasks in the controller.
	 *
	 */
	public function setDoTask( $task )
	{
		$this->doTask = $task;
	}

	/**
	 * Method to load and return a model object.
	 *
	 * @access	private
	 * @param	string  The name of the model.
	 * @param	string	Optional model prefix.
	 * @param	array	Configuration array for the model. Optional.
	 * @return	mixed	Model object on success; otherwise null
	 * failure.
	 * @since	1.5
	 */
	function _createModel( $name, $prefix = '', $config = array() )
	{
		// Clean the model name
		$modelName = preg_replace( '/[^A-Z0-9_]/i', '', $name );
		$classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );

		$result = StratumModel::getInstance( $modelName, $classPrefix, $config );
		return $result;
	}

	/**
	 * Displays item
	 * @return void
	 */
	protected function displayView( $cachable = false, $urlparams = false )
	{
		$model = $this->getModel( $this->get( 'suffix' ) );
		$model->getId( );
		$row = $model->getItem( );

		$view = $this->getView( $this->get( 'suffix' ), 'html' );
		$view->setModel( $model, true );
		$view->row = $row;
		$view->setLayout( 'view' );

		$model->emptyState( );
		$this->_setModelState( );
		$surrounding = $model->getSurrounding( $model->getId( ) );
		$view->surrounding = $surrounding;

		$view->setTask( true );

		// TODO take into account the $cachable value, as in $this->display();

		$view->display( );
		$this->footer( );
		return $this;
	}

	/**
	 * Checks if an item is checked out, and if so, redirects to layout for viewing item
	 * Otherwise, displays a form for editing item
	 *
	 * @return void
	 */
	protected function displayEdit( $cachable = false, $urlparams = false )
	{
		$view = $this->getView( $this->get( 'suffix' ), 'html' );
		$model = $this->getModel( $this->get( 'suffix' ) );
		$row = $model->getTable( );
		$row->load( $model->getId( ) );
		$userid = Factory::getApplication()->getIdentity( )->id;

		// Checks if item is checkedout, and if so, redirects to view
		if ( empty( $row->id ) )
		{
			$this->input->set( 'hidemainmenu', '1' );
			$view->setLayout( 'form' );
		} elseif ( !Table::isCheckedOut( $userid, $row->checked_out ) )
		{
			if ( $row->checkout( $userid ) )
			{
				$this->input->set( 'hidemainmenu', '1' );
				$view->setLayout( 'form' );
			} else
			{
				$view->setLayout( 'view' );
			}
		} else
		{
			$view->setLayout( 'view' );
		}

		$view->setModel( $model, true );
		$row = $model->getItem( $row->id, true );
		$view->row = $row;

		$model->emptyState( );
		$this->_setModelState( );
		$surrounding = $model->getSurrounding( $model->getId( ) );
		$view->surrounding = $surrounding;

		$view->setTask( true );

		// TODO take into account the $cachable value, as in $this->display();

		$view->display( );
		$this->footer( );
		return $this;
	}

	/**
	 * Releases an item from being checked out for editing
	 * @return unknown_type
	 */
	function release( )
	{
		$model = $this->getModel( $this->get( 'suffix' ) );
		$row = $model->getTable( );
		$row->load( $model->getId( ) );
		if ( isset( $row->checked_out ) && !Table::isCheckedOut( Factory::getApplication()->getIdentity( )->id, $row->checked_out ) )
		{
			if ( $row->checkin( ) )
			{
				$this->message = Text::_( "LIB_STRATUM_ITEM_RELEASED" );
			}
		}

		$redirect = "index.php?option=" . $this->get( 'com' ) . "&controller=" . $this->get( 'suffix' ) . "&view=" . $this->get( 'suffix' ) . "&task=view&id=" . $model->getId( ) . "&donotcheckout=1";
		$redirect = Route::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

	/**
	 * Cancels operation and redirects to default page
	 * If item is checked out, releases it
	 * @return void
	 */
	function cancel( )
	{
		if ( !isset( $this->redirect ) )
		{
			$this->redirect = 'index.php?option=' . $this->get( 'com' ) . '&view=' . $this->get( 'suffix' );
		}

		$task = $this->input->getCmd( 'task' );
		switch (strtolower($task))
		{
			case "cancel":
				$msg = Text::_( 'LIB_STRATUM_OPERATION_CANCELLED' );
				$type = "notice";
				break;
			case "close":
			default:
				$model = $this->getModel( $this->get( 'suffix' ) );
				$row = $model->getTable( );
				$row->load( $model->getId( ) );
				if ( isset( $row->checked_out ) && !Table::isCheckedOut( Factory::getApplication()->getIdentity( )->id, $row->checked_out ) )
				{
					$row->checkin( );
				}
				$msg = "";
				$type = "";
				break;
		}

		$this->setRedirect( $this->redirect, $msg, $type );
	}

	/**
	 * Verifies the fields in a submitted form.  Uses the table's check() method.
	 * Will often be overridden. Is expected to be called via Ajax
	 *
	 * @return unknown_type
	 */
	function validate( )
	{
		$response = array( );
		$response['msg'] = '';
		$response['error'] = '';

		// get elements from post
		$elements = json_decode( preg_replace( '/[\n\r]+/', '\n', $this->input->post->getString( 'elements' ) ) );

		// convert elements to array that can be binded
		$helper = new StratumHelper( );
		$values = $helper->elementsToArray( $elements );

		// get table object
		$table = $this->getModel( $this->get( 'suffix' ) )->getTable( );

		// bind to values
		$table->bind( $values );

		// validate it using table's ->check() method
		if ( !$table->check( ) )
		{
			$string = '';
			// if it fails check, return message
			$response['error'] = '1';
			foreach ( $table->getErrors() as $error )
			{
				$string .= "<li>" . $error . "</li>";
			}
			$response['msg'] = $helper->generateMessage( $string, false );
		}

		echo( json_encode( $response ));
		return;
	}

	/**
	 * Displays the footer
	 *
	 * @return unknown_type
	 */
	function footer( )
	{
		//check if show the footer
		$show_footer = $this->input->getBool('show_footer', $this->show_footer);		
		if(!$show_footer) return '';
				
		$model = $this->getModel( 'dashboard' );
		$view = $this->getView( 'dashboard', 'html' );

		// Dispatch the onAfterFooter event
		$eventNameAfterFooter = 'onAfterFooter';
		$eventAfterFooter = new Event($eventNameAfterFooter, ['subject' => $this]);
		Factory::getDispatcher()->dispatch($eventNameAfterFooter, $eventAfterFooter);
		$results = $eventAfterFooter->getResults(); // J4 events often store results this way

		$html = implode( '<br />', $results );

		$view->hidemenu = true;
		$view->hidestats = true;
		$view->setModel( $model, true );
		$view->setLayout( 'footer' );
		$view->setTask( true );
		$view->no_state = true;
		$view->no_pagination = true;
		$view->no_items = true;	
		$view->extraHtml = $html;
		$view->display( );
	}

	/**
	 * Executes a specified task from within a plugin.
	 * Usage: index.php?option=com_sample&task=doTask&element=pluginname&elementTask=pluginfunction
	 *
	 * @return HTML output from plugin
	 */
	function doTask( )
	{
		$success = true;
		$msg = new stdClass( );
		$msg->message = '';
		$msg->error = '';

		// expects $element in URL and $elementTask
		$element = $this->input->getString( 'element' );
		$elementTask = $this->input->getString( 'elementTask' );

		$msg->error = '1';
		// $msg->message = "element: $element, elementTask: $elementTask";

		// gets the plugin named $element
		$import = PluginHelper::importPlugin( $this->_name, $element );
		$dispatcher = Factory::getDispatcher( );

		// executes the event $elementTask for the $element plugin
		// returns the html from the plugin
		// passing the element name allows the plugin to check if it's being called (protects against same-task-name issues)
		$eventArguments = ['element' => $element, 'subject' => $this];
		$event = new Event($elementTask, $eventArguments);
		Factory::getDispatcher()->dispatch($elementTask, $event);
		$result = $event->getResults();

		// This should be a concatenated string of all the results,
		// in case there are many plugins with this eventname
		// that return null b/c their filename != element)
		$msg->message = implode( '', $result );
		// $msg->message = @$result['0'];

		echo $msg->message;
		$success = $msg->message;

		return $success;
	}

	/**
	 * Executes a specified task from within a plugin and returns results json_encoded (for ajax implementation).
	 * Usage: index.php?option=com_sample&task=doTaskAjax&element=pluginname&elementTask=pluginfunction
	 *
	 * @return array(msg=>HTML output from plugin)
	 */
	function doTaskAjax( )
	{
		JLoader::import( 'stratum.tools.json', JPATH_SITE . '/libraries' );

		$success = true;
		$msg = new stdClass( );
		$msg->message = '';

		// get elements $element and $elementTask in URL
		$element = $this->input->getString( 'element' );
		$elementTask = $this->input->getString( 'elementTask' );

		// allow $element to be in format file_name.group_name
		$exploded = explode( '.', $element );
		$element = $exploded[0];
		$elementGroup = empty( $exploded[1] ) ? $this->_name : $exploded[1];

		jimport( 'joomla.plugin.helper' ); // This jimport is for Joomla's plugin helper, replaced by use statement.
		PluginHelper::importPlugin( $elementGroup );

		// gets the plugin named $element
		$import = PluginHelper::importPlugin( $elementGroup, $element );
		$dispatcher = Factory::getDispatcher( );

		// executes the event $elementTask for the $element plugin
		// returns the html from the plugin
		// passing the element name allows the plugin to check if it's being called (protects against same-task-name issues)
		$result = $dispatcher->trigger( $elementTask, array( $element ) );

		// This should be a concatenated string of all the results,
		// in case there are many plugins with this eventname
		// that return null b/c their filename != element)
		$msg->message = implode( '', $result );
		// $msg->message = @$result['0'];

		// set response array
		$response = array( );
		$response['msg'] = $msg->message;

		// encode and echo (need to echo to send back to browser)
		echo( json_encode( $response ));

		return $success;
	}

	/**
	 * For displaying a searchable list of articles in a lightbox
	 * Usage:
	 */
	function elementArticle( )
	{
		$model = $this->getModel( 'elementarticle' );
		$view = $this->getView( 'elementarticle' );
		include_once (JPATH_ADMINISTRATOR . '/components/com_content/helper.php');
		$view->setModel( $model, true );
		$view->setTask( true );
		$view->display( );
	}

	/**
	 * For displaying a searchable list of users in a lightbox
	 * Usage:
	 */
	function elementUser( )
	{
		$model = $this->getModel( 'elementuser' );
		$view = $this->getView( 'elementuser' );
		$view->setModel( $model, true );
		$view->setTask( true );
		$view->display( );
	}

	/**
	 * Simple function for checking that a request is not being forged, and comes from the current user
	 *
	 * @param unknown_type $raiseError
	 */
	protected function checkToken( $raiseError = true )
	{
		$tokenValid = Session::checkToken( );

		if ( $tokenValid )
		{
			return true;
		}

		if ( $raiseError )
		{
			Factory::getApplication()->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
            throw new InvalidTokenException(Text::_('JINVALID_TOKEN'), 403);
		}

		return false;
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 */
	protected function allowAdd( $data = array(), $key = null )
	{
		$user = Factory::getApplication()->getIdentity( );
		return $user->authorise( 'core.create', $this->option );
	}

	/**
	 * Method to check if you can edit a record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 */
	protected function allowEdit( $data = array(), $key = 'id' )
	{
		return Factory::getApplication()->getIdentity( )->authorise( 'core.edit', $this->option );
	}

	/**
	 * Method to check if you can edit the state of a record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 */
	protected function allowEditState( $data = array(), $key = 'id' )
	{
		return Factory::getApplication()->getIdentity( )->authorise( 'core.edit.state', $this->option );
	}

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 */
	protected function allowSave( $data, $key = 'id' )
	{
		// Initialise variables.
		$recordId = isset( $data[$key] ) ? $data[$key] : '0';

		if ( $recordId )
		{
			return $this->allowEdit( $data, $key );
		} else
		{
			return $this->allowAdd( $data );
		}
	}

	/**
	 * Method to check if you can delete a record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 */
	protected function allowDelete( $data = array(), $key = 'id' )
	{
		return Factory::getApplication()->getIdentity( )->authorise( 'core.delete', $this->option );
	}

	/**
	 * Method to check if you can view a record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   2.0
	 */
	protected function allowView( $data = array(), $key = 'id' )
	{
		return Factory::getApplication()->getIdentity( )->authorise( 'core.view', $this->option );
	}

	/**
	 * Saves an item and redirects based on task.
	 * Should be called from a public function in your controller.
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 *
	 * @return boolean
	 */
	protected function doSave( )
	{
		$model = $this->getModel( $this->get( 'suffix' ) );
		$row = $model->getTable( );
		$row->load( $model->getId( ) );
		$post = $this->input->getArray( $_POST );
		$row->bind( $post );
		$task = $this->input->getCmd( 'task' );

		if ( $task == "save_as" )
		{
			$pk = $row->getKeyName( );
			$row->$pk = 0;
		}

		// TODO add this access check
		/*
		 $key = $row->getKeyname();
		 jimport('joomla.utilities.arrayhelper');
		 if (!$this->allowSave(JArrayHelper::fromObject($row), $key))
		 {
		 $this->setError(JText::_('STRATUM_ERROR_SAVE_NOT_PERMITTED'));
		 $this->setMessage($this->getError(), 'error');

		 $this->setRedirect( JRoute::_('index.php?option=' . $this->get('com') . '&view=' . $this->get( 'suffix' ), false ) );

		 return false;
		 }
		 */

		if ( $row->save( ) )
		{
			$model->setId( $row->id );
			$model->clearCache( );

			$this->messagetype = 'message';
			$this->message = Text::_( 'LIB_STRATUM_SAVED' );

            // Dispatch the onAfterSave event
            $eventNameOnAfterSave = 'onAfterSave' . $this->get( 'suffix' );
            $eventOnAfterSave = new Event($eventNameOnAfterSave, ['subject' => $this, 'row' => $row]);
            Factory::getDispatcher()->dispatch($eventNameOnAfterSave, $eventOnAfterSave);

			$return = $row;
		} else
		{
			$app = Factory::getApplication( );
			$this->messagetype = 'notice';
			$this->message = Text::_( 'LIB_STRATUM_SAVE_FAILED' );
			if ( $errors = $row->getErrors( ) )
			{
				foreach ( $errors as $error )
				{
					if ( !empty( $error ) )
					{
						$app->enqueueMessage( $error, 'notice' );
					}
				}
			}

			$return = false;
		}

		$redirect = "index.php?option=" . $this->get( 'com' );

		switch ($task)
		{
			case "save_as":
				$redirect .= '&view=' . $this->get( 'suffix' ) . '&task=edit&id=' . $row->id;
				$this->message .= " - " . Text::_( 'LIB_STRATUM_YOU_ARE_NOW_EDITING_THE_NEW_ITEM' );
				break;
			case "saveprev":
				$redirect .= '&view=' . $this->get( 'suffix' );
				// get prev in list
				$model->emptyState( );
				$this->_setModelState( );
				$surrounding = $model->getSurrounding( $model->getId( ) );
				if ( !empty( $surrounding['prev'] ) )
				{
					$redirect .= '&task=edit&id=' . $surrounding['prev'];
				}
				break;
			case "savenext":
				$redirect .= '&view=' . $this->get( 'suffix' );
				// get next in list
				$model->emptyState( );
				$this->_setModelState( );
				$surrounding = $model->getSurrounding( $model->getId( ) );
				if ( !empty( $surrounding['next'] ) )
				{
					$redirect .= '&task=edit&id=' . $surrounding['next'];
				}
				break;

			case "savenew":
				$redirect .= '&view=' . $this->get( 'suffix' ) . '&task=add';
				break;
			case "apply":
				$redirect .= '&view=' . $this->get( 'suffix' ) . '&task=edit&id=' . $model->getId( );
				break;
			case "save":
			default:
				$redirect .= "&view=" . $this->get( 'suffix' );
				break;
		}

		$redirect = Route::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );

		return $return;
	}

	/**
	 * Deletes an item and redirects based on task
	 * Should be called from a public function in your controller.
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 *
	 * @return boolean
	 */
	protected function doDelete( )
	{
		$error = false;
		$this->messagetype = '';
		$this->message = '';
		if ( !isset( $this->redirect ) )
		{
			$return = $this->input->get( 'return', '', 'BASE64' );
			$this->redirect = $return ? base64_decode( $return ) : 'index.php?option=' . $this->get( 'com' ) . '&view=' . $this->get( 'suffix' );
			$this->redirect = Route::_( $this->redirect, false );
		}

		$model = $this->getModel( $this->get( 'suffix' ) );
		$row = $model->getTable( );

		$cids = $this->input->get( 'cid', array( 0 ), 'array' );

		foreach ( @$cids as $cid )
		{
			if ( !$row->delete( $cid ) )
			{
				$this->message .= $row->getError( );
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ( $error )
		{
			$this->message = Text::_( 'LIB_STRATUM_ERROR' ) . " - " . $this->message;
			$return = false;
		} else
		{
			$this->message = Text::_( 'LIB_STRATUM_ITEMS_DELETED' );
			$return = true;
		}

		$model->clearCache( );
		$this->setRedirect( $this->redirect, $this->message, $this->messagetype );

		return $return;
	}

	/**
	 * Reorders a single item either up or down (based on arrow-click in list) and redirects to default layout
	 * Should be called from a public function in your controller.
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 *
	 * @return boolean
	 */
	protected function doOrder( )
	{
		$error = false;
		$this->messagetype = '';
		$this->message = '';
		$redirect = 'index.php?option=' . $this->get( 'com' ) . '&view=' . $this->get( 'suffix' );
		$redirect = Route::_( $redirect, false );

		$model = $this->getModel( $this->get( 'suffix' ) );
		$row = $model->getTable( );
		$row->load( $model->getId( ) );

		$change = $this->input->post->getInt( 'order_change', '0' );

		$return = true;
		if ( !$row->move( $change ) )
		{
			$this->messagetype = 'notice';
			$this->message = Text::_( 'LIB_STRATUM_ORDERING_FAILED' ) . " - " . $row->getError( );
			$return = false;
		}

		$model->clearCache( );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );

		return $return;
	}

	/**
	 * Reorders multiple items (based on form input from list) and redirects to default layout
	 * Should be called from a public function in your controller.
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 *
	 * @return boolean
	 */
	protected function doOrdering( )
	{
		$error = false;
		$this->messagetype = '';
		$this->message = '';
		$redirect = 'index.php?option=' . $this->get( 'com' ) . '&view=' . $this->get( 'suffix' );
		$redirect = Route::_( $redirect, false );

		$model = $this->getModel( $this->get( 'suffix' ) );
		$row = $model->getTable( );

		$ordering = $this->input->post->get( 'ordering', array( 0 ), 'array' );
		$cids = $this->input->post->get( 'cid', array( 0 ), 'array' );
		foreach ( @$cids as $cid )
		{
			$row->load( $cid );
			$row->ordering = @$ordering[$cid];

			if ( !$row->store( ) )
			{
				$this->message .= $row->getError( );
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		$row->reorder( );

		if ( $error )
		{
			$this->message = Text::_( 'LIB_STRATUM_ERROR' ) . " - " . $this->message;
			$return = false;
		} else
		{
			$this->message = Text::_( 'LIB_STRATUM_ITEMS_ORDERED' );
			$return = true;
		}

		$model->clearCache( );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
		return $return;
	}

	/**
	 * Changes the value of a boolean in the database
	 * Expects the task to be in the format: {field}.{action}
	 * where {field} = the name of the field in the database
	 * and {action} is either switch/enable/disable
	 *
	 * Should be called from a public function in your controller.
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 *
	 * @return boolean
	 */
	protected function doBoolean( )
	{
		$error = false;
		$this->messagetype = '';
		$this->message = '';
		$redirect = 'index.php?option=' . $this->get( 'com' ) . '&view=' . $this->get( 'suffix' );
		$redirect = Route::_( $redirect, false );

		$model = $this->getModel( $this->get( 'suffix' ) );
		$row = $model->getTable( );

		$cids = $this->input->post->get( 'cid', array( 0 ), 'array' );
		$task = $this->input->getCmd( 'task' );
		$vals = explode( '.', $task );

		$field = $vals['0'];
		$action = $vals['1'];

		switch (strtolower($action))
		{
			case "switch":
				$switch = '1';
				break;
			case "disable":
				$enable = '0';
				$switch = '0';
				break;
			case "enable":
				$enable = '1';
				$switch = '0';
				break;
			default:
				$this->messagetype = 'notice';
				$this->message = Text::_( "LIB_STRATUM_INVALID_TASK" );
				$this->setRedirect( $redirect, $this->message, $this->messagetype );
				return;
				break;
		}

		if ( !in_array( $field, array_keys( $row->getProperties( ) ) ) )
		{
			$this->messagetype = 'notice';
			$this->message = Text::_( "LIB_STRATUM_INVALID_FIELD" ) . ": {$field}";
			$this->setRedirect( $redirect, $this->message, $this->messagetype );
			return;
		}

		foreach ( @$cids as $cid )
		{
			unset( $row );
			$row = $model->getTable( );
			$row->load( $cid );

			switch ($switch)
			{
				case "1":
					$row->$field = $row->$field ? '0' : '1';
					break;
				case "0":
				default:
					$row->$field = $enable;
					break;
			}

			if ( !$row->save( ) )
			{
				$this->message .= $row->getError( );
				$this->messagetype = 'notice';
				$error = true;
			}
		}

		if ( $error )
		{
			$this->message = Text::_( 'LIB_STRATUM_ERROR' ) . ": " . $this->message;
			$return = false;
		} else
		{
			$this->message = Text::_( 'LIB_STRATUM_STATUS_CHANGED' );
			$return = true;
		}

		$model->clearCache( );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
		return $return;
	}

	/**
	 * Changes a boolean field, is a wrapper for boolean
	 * Should be called from a public function in your controller.
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 *
	 * @return boolean
	 */
	protected function doEnable( )
	{
		$task = $this->input->getCmd( 'task' );

		switch (strtolower($task))
		{
			case "switch_publish":
				$field = 'published';
				$action = 'switch';
				break;
			case "switch":
			case "switch_enable":
				$field = 'enabled';
				$action = 'switch';
				break;
			case "unpublish":
				$field = 'published';
				$action = 'disable';
				break;
			case "disable":
				$field = 'enabled';
				$action = 'disable';
				break;
			case "publish":
				$field = 'published';
				$action = 'enable';
				break;
			case "enable":
			default:
				$field = 'enabled';
				$action = 'enable';
				break;
		}

		$this->input->set( 'task', $field . '.' . $action );
		return $this->boolean( );
	}

	/**
	 * Checks in the current item and displays the previous/next one in the list
	 * Should be called from a public function in your controller.
	 *
	 * It is the responsibility of each child controller to check the validity of the request using
	 * (j1.6+) JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	 *
	 * @return boolean
	 */
	protected function doJump( )
	{
		$model = $this->getModel( $this->get( 'suffix' ) );
		$id = $model->getId( );
		$row = $model->getTable( );
		$row->load( $id );
		if ( isset( $row->checked_out ) && !Table::isCheckedOut( Factory::getApplication()->getIdentity( )->id, $row->checked_out ) )
		{
			$row->checkin( );
		}
		$task = $this->input->getCmd( 'task' );
		$redirect = "index.php?option=" . $this->get( 'com' ) . "&view=" . $this->get( 'suffix' );

		$model->emptyState( );
		$this->_setModelState( );
		$surrounding = $model->getSurrounding( $id );

		switch ($task)
		{
			case "prev":
				if ( !empty( $surrounding['prev'] ) )
				{
					$redirect .= "&task=view&id=" . $surrounding['prev'];
				}
				break;
			case "next":
				if ( !empty( $surrounding['next'] ) )
				{
					$redirect .= "&task=view&id=" . $surrounding['next'];
				}
				break;
		}
		$redirect = Route::_( $redirect, false );
		$this->setRedirect( $redirect, $this->message, $this->messagetype );
	}

    /**
     * Gets the controller name.
     *
     * The dispatcher is responsible for parsing the GET variable `view` and instantiating
     * the controller class. The name of the controller is the value of this `view` variable.
     * This value is passed by the dispatcher to the constructor of the controller.
     *
     * @return  string  The name of this controller.
     *
     * @since   Legacy
     * @note    This is a basic implementation. BaseController sets $this->name.
     *          StratumController also sets $this->_name from the component.
     *          This method prioritizes Stratum's _name if available.
     */
    public function getName()
    {
        return $this->_name ?: $this->name;
    }
}
