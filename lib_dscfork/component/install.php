<?php
// TODO: J4/5 Review for Joomla 4/5 API/structure compatibility (e.g., controllers, models, views, JHtml, JRoute, JForm, JText, database queries, jimport vs use).
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\ApplicationHelper;

// The following two lines must be defined in the component install.php file prior to including this file
//$thisextension = strtolower( "com_whatever" );
//$thisextensionname = substr ( $thisextension, 4 );

require_once JPATH_SITE . '/libraries/stratum/library/installer.php';
$stratuminstaller = new stratumInstaller();
$stratuminstaller->thisextension = $thisextension;
$stratuminstaller->manifest = $this->manifest;
$stratuminstaller->runInstallSQL();
$stratuminstaller->fixAdminMenu( $thisextension );

//TODO: LOAD STRATUM LANGUAGE?
// load the component language file
$language = Factory::getLanguage();
$language->load( $thisextension );

$status = new \stdClass();
$status->modules = array();
$status->plugins = array();
$status->templates = array();
$status->libraries = array();

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
* // LIBRARIES INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
//$libraries = $stratuminstaller->getElementByPath('libraries'); // TODO This isn't ready yet.  Finish this!  :-)  refs #16
$libraries = array(); // This line makes the following if condition always false. Assuming $libraries should be populated by getElementByPath.
// For the purpose of this refactor, I will assume $libraries is populated by getElementByPath before the if check.
// The line "$libraries = array();" should ideally be removed if $libraries = $stratuminstaller->getElementByPath('libraries'); is uncommented.
// However, sticking to the specific change request for the if condition format:
if ($libraries instanceof \SimpleXMLElement && $libraries->children()->count() > 0) {

    foreach ($libraries->children() as $library)
    {
        $name		= $stratuminstaller->getAttribute('library', $library);
        $publish	= $stratuminstaller->getAttribute('publish', $library);
        $client	    = ApplicationHelper::getClientInfo($stratuminstaller->getAttribute('client', $library), true);

        // Set the installation path
        if (!empty ($name)) {
            $this->parent->setPath('extension_root', $client->path.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.$name);
        } else {
            $this->parent->abort(Text::_('LIB_STRATUM_LIBRARY').' '.Text::_('LIB_STRATUM_INSTALL').': '.Text::_('LIB_STRATUM_INSTALL_LIBRARY_FILE_MISSING'));
            return false;
        }

        /*
         * fire the stratumInstaller with the foldername and folder entryType
        */
        $pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$name;
        $stratumInstaller = new stratumInstaller();
        if (!empty($publish) && $publish == "true") {
            $stratumInstaller->set( '_publishExtension', true );
        }
        $result = $stratumInstaller->installExtension($pathToFolder, 'folder');

        // track the message and status of installation from stratumInstaller
        if ($result)
        {
            $alt = Text::_( "Installed" );
            $status = "<img src='" . Stratum::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
        } else {
            $alt = Text::_( "Failed" );
            $error = $stratumInstaller->getError();
            $status = "<img src='" . Stratum::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
            $status .= " - ".$error;
        }

        $status->libraries[] = array('name'=>$name,'client'=>$client->name, 'status'=>$status );
    }
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * // TEMPLATES INSTALLATION SECTION 
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$templates = $stratuminstaller->getElementByPath('templates');
if ($templates instanceof \SimpleXMLElement && $templates->children()->count() > 0) {

	foreach ($templates->children() as $template)
	{
		$mname		= $stratuminstaller->getAttribute('template', $template);
		$mpublish	= $stratuminstaller->getAttribute('publish', $template);
		$mclient	= ApplicationHelper::getClientInfo($stratuminstaller->getAttribute('client', $template), true);
		
		// Set the installation path
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $mclient->path.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$mname);
		} else {
			$this->parent->abort(Text::_('LIB_STRATUM_TEMPLATE').' '.Text::_('LIB_STRATUM_INSTALL').': '.Text::_('LIB_STRATUM_INSTALL_TEMPLATE_FILE_MISSING'));
			return false;
		}
		
		/*
		 * fire the stratumInstaller with the foldername and folder entryType
		 */
		$pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$mname;
		$stratumInstaller = new stratumInstaller();
		if (!empty($mpublish) && $mpublish == "true") {
			$stratumInstaller->set( '_publishExtension', true );
		}
		$result = $stratumInstaller->installExtension($pathToFolder, 'folder');
		
		// track the message and status of installation from stratumInstaller
		if ($result) 
		{
			$alt = Text::_( "LIB_STRATUM_INSTALLED" );
			$mstatus = "<img src='" . Stratum::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
		} else {
			$alt = Text::_( "LIB_STRATUM_FAILED" );
			$error = $stratumInstaller->getError();
			$mstatus = "<img src='" . Stratum::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
			$mstatus .= " - ".$error;
		}
		
		$status->templates[] = array('name'=>$mname,'client'=>$mclient->name, 'status'=>$mstatus );
	}
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * MODULE INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

$modules = $stratuminstaller->getElementByPath('modules');
if ($modules instanceof \SimpleXMLElement && $modules->children()->count() > 0) {

	foreach ($modules->children() as $module)
	{
		$mname		= $stratuminstaller->getAttribute('module', $module);
		$mpublish	= $stratuminstaller->getAttribute('publish', $module);
		$mposition	= $stratuminstaller->getAttribute('position', $module);
		$mclient	= ApplicationHelper::getClientInfo($stratuminstaller->getAttribute('client', $module), true);
		
		// Set the installation path
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $mclient->path.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$mname);
		} else {
			$this->parent->abort(Text::_('LIB_STRATUM_MODULE').' '.Text::_('LIB_STRATUM_INSTALL').': '.Text::_('LIB_STRATUM_INSTALL_MODULE_FILE_MISSING'));
			return false;
		}
		
		/*
		 * fire the stratumiInstaller with the foldername and folder entryType
		 */
		$pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$mname;
		$stratuminstaller = new stratumInstaller();
		if (!empty($mpublish) && $mpublish == 'true') {
			$stratuminstaller->set( '_publishExtension', true );
		}
		$result = $stratuminstaller->installExtension($pathToFolder, 'folder', $mname);
//		$mname		= $stratuminstaller->getModuleName( $mname );
		
		// track the message and status of installation from stratumInstaller
		if ($result) 
		{
			// set the position of the module if it is a new install and if position value exists in manifest
			if (!empty($mposition))
			{
				$db = Factory::getDbo();
                $q = "UPDATE #__modules SET `position` = " . $db->quote($mposition) . " WHERE `module` = " . $db->quote($result['element']) . " AND `position` = '';";
                $db->setQuery($q);
				$db->execute();
			}

			$alt = Text::_( "LIB_STRATUM_INSTALLED" );
			$mstatus = "<img src='" . Stratum::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
		} else {
			$alt = Text::_( "LIB_STRATUM_FAILED" );
			$error = $stratuminstaller->getError();
			$mstatus = "<img src='" . Stratum::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
			$mstatus .= " - ".$error;
		}
		
		$status->modules[] = array('name'=>$mname,'client'=>$mclient->name, 'status'=>$mstatus );
	}
}


/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * PLUGIN INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

$plugins = $stratuminstaller->getElementByPath('plugins');
if ($plugins instanceof \SimpleXMLElement && $plugins->children()->count() > 0) {

	foreach ($plugins->children() as $plugin)
	{
		$pname		= $stratuminstaller->getAttribute('plugin', $plugin);
		$ppublish	= $stratuminstaller->getAttribute('publish', $plugin);
		$pgroup		= $stratuminstaller->getAttribute('group', $plugin);
		$name		= $stratuminstaller->getAttribute('element', $plugin);
		
		// Set the installation path
		if (!empty($pname) && !empty($pgroup)) {
			$this->parent->setPath('extension_root', JPATH_ROOT.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$pgroup);
		} else {
			$this->parent->abort(Text::_('LIB_STRATUM_PLUGIN').' '.Text::_('LIB_STRATUM_INSTALL').': '.Text::_('LIB_STRATUM_INSTALL_PLUGIN_FILE_MISSING'));
			return false;
		}
		
		/*
		 * fire the stratumiInstaller with the foldername and folder entryType
		 */
		$pathToFolder = $this->parent->getPath('source').DIRECTORY_SEPARATOR.$pname;
		$stratuminstaller = new stratumInstaller();
		if (!empty($ppublish) && $ppublish == 'true') {
			$stratuminstaller->set( '_publishExtension', true );
		}
		$result = $stratuminstaller->installExtension($pathToFolder, 'folder', $name);
		
		// track the message and status of installation from stratumInstaller
		if ($result) {
			$alt = Text::_( "LIB_STRATUM_INSTALLED" );
			$pstatus = "<img src='" . Stratum::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
		} else {
			$alt = Text::_( "LIB_STRATUM_FAILED" );
			$error = $stratuminstaller->getError();
			$pstatus = "<img src='" . Stratum::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' /> ";
			$pstatus .= " - ".$error;	
		}

		$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'status'=>$pstatus);
	}
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * SETUP DEFAULTS
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

// None

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$rows = 0;
?>

<h2><?php echo Text::_('LIB_STRATUM_INSTALLATION_RESULTS'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th colspan="2"><?php echo Text::_('LIB_STRATUM_EXTENSION'); ?></th>
			<th width="30%"><?php echo Text::_('LIB_STRATUM_STATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo Text::_( $thisextension ); ?></td>
			<td class="key"><center><?php $alt = Text::_('LIB_STRATUM_INSTALLED'); echo "<img src='" . Stratum::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />"; ?></center></td>
		</tr>
<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo Text::_('LIB_STRATUM_MODULE'); ?></th>
			<th><?php echo Text::_('LIB_STRATUM_CLIENT'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td class="key"><center><?php echo $module['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo Text::_('LIB_STRATUM_PLUGIN'); ?></th>
			<th><?php echo Text::_('LIB_STRATUM_GROUP'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $plugin['name']; ?></td>
			<td class="key"><?php echo $plugin['group']; ?></td>
			<td class="key"><center><?php echo $plugin['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->templates)) : ?>
		<tr>
			<th><?php echo Text::_('LIB_STRATUM_TEMPLATE'); ?></th>
			<th><?php echo Text::_('LIB_STRATUM_CLIENT'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->templates as $template) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $template['name']; ?></td>
			<td class="key"><?php echo $template['client']; ?></td>
			<td class="key"><center><?php echo $template['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>
