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

$mainframe = JFactory::getApplication( );

//get current app and load
$stratumApp = Stratum::getApp( );
$app = ucfirst( $stratumApp->getName( ) );

//TODO: add config if we do the diagnostics
// before executing any tasks, check the integrity of the installation
$app::getClass( $app . 'HelperDiagnostics', 'helpers.diagnostics' )->checkInstallation( );

// Require the base controller
$app::load( $app . 'Controller', 'controller' );

// Check if protocol is specified
$protocol = $mainframe->input->getWord( 'protocol', '' );

// Require specific controller if requested
$controller = $mainframe->input->getWord( 'controller', $mainframe->input->getCmd( 'view' ) );

// if protocol is specified, try to load the specific controller
if( strlen( $protocol ) )
{
	// file syntax: controller_json.php
	if( $app::load( $app . 'Controller' . $controller . $protocol, "controllers." . $controller . "_" . $protocol ) )
	{
		$controller .= $protocol;
	}
} else
{
	if( !$app::load( $app . 'Controller' . $controller, "controllers.$controller" ) )
	{
		$controller = '';
	}
}

if( empty( $controller ) )
{
	// redirect to default
	$class = $app . 'Controller';
	$default_controller = new $class( );
	$redirect = "index.php?option=com_" . strtolower( $app ) . "&view=" . $default_controller->default_view;
	$redirect = JRoute::_( $redirect, false );
	$mainframe->redirect( $redirect );
}

//add component js namespace
$doc = JFactory::getDocument( );
$uri = JURI::getInstance( );
$js = "var com_" . strtolower( $app ) . " = {};\n";
$js .= "com_" . strtolower( $app ) . ".jbase = '" . $uri->root( ) . "';\n";
$doc->addScriptDeclaration( $js );

//add common js and css
JHTML::_( 'script', 'common.js', 'media/stratum/js/' );
JHTML::_( 'stylesheet', 'common.css', 'media/stratum/css/' );

//register component helpers
$parentPath = JPATH_ADMINISTRATOR . '/components/com_' . strtolower( $app ) . '/helpers';
StratumLoader::discover( $app . 'Helper', $parentPath, true );

//register component libary
$parentPath = JPATH_ADMINISTRATOR . '/components/com_' . strtolower( $app ) . '/library';
StratumLoader::discover( $app, $parentPath, true );

// load the plugins
JPluginHelper::importPlugin( strtolower( $app ) );

// Create the controller
$classname = $app . 'Controller' . $controller;
$controller = $app::getClass( $classname );

// ensure a valid task exists
$task = $mainframe->input->getCmd( 'task' );
if( empty( $task ) )
{
	$task = 'display';
}

$mainframe->input->set( 'task', $task );

//check if hidemainmenu. example for edit view
$hidemainmenu = $mainframe->input->getBool( 'hidemainmenu' );

//check if tmpl=component
$tmpl = $mainframe->input->getWord( 'tmpl' );

if($hidemainmenu || strtolower($tmpl) == 'component'):
	
	// Perform the requested task
	$controller->execute( $task );
	
	// Redirect if set by the controller
	$controller->redirect();
	
else:
?>

<div id="dscfork-container">
	
	<div class="stratum-header stratum-clearfix">
		<div class="container-fluid">
			<div class="container-inner">
				<div class="stratum-logo stratum-left">
					<a href="<?php echo $app::getAppUrl();?>"><img src="<?php echo $app::getURL( 'images' ); ?>logo_main.png" class="app-logo"/></a>
				</div>
				<div class="stratum-right">
					<?php
						$modules = JModuleHelper::getModules("stratum_header_right");
					
						$document	= JFactory::getDocument();
						$renderer	= $document->loadRenderer('module');
						$attribs 	= array();
						$attribs['style'] = 'none';
						foreach ( @$modules as $mod )
						{
							echo $renderer->render($mod, $attribs);
						}
					?>
					
				</div>				
			</div>
			
		</div>
		
	</div>
	
	<div class="stratum-admin-wrapper">
		
		<div id="stratum-sidebar" class="stratum-sidebar">
		<?php
			$modules = JModuleHelper::getModules("stratum_sidebar");
		
			foreach ( @$modules as $mod )
			{
				echo $renderer->render($mod, $attribs);
			}
		?>
		</div>
	
		<div class="stratum-main">
		
			<div class="app-header stratum-clearfix">
				<h2 class="app-title stratum-left">
					<!-- TITLE WILL BE INSERTED HERE -->
				</h2>				
				<!-- TOOLBAR BUTTONS WILL BE INSERTED HERE -->				
			</div>
			
			<div class="app-content">
			<?php
			$controller->execute( $task );
			$controller->redirect( );
			?>
			</div>
		
		</div>
	
	</div>

</div>
<script type="text/javascript">
//move page title
jQuery("#stratum-container .app-title").prepend(stratum.strip_tags(jQuery("h1.page-title").html()));

//move toolbar
jQuery("#toolbar").addClass("stratum-right stratum-margin-none app-toolbar").prependTo(".app-header");
</script>

<?php endif;?>