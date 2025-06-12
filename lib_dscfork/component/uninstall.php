<?php defined( '_JEXEC' ) or die( 'Restricted access' );
// The following two lines must be defined in the component install.php file prior to including this file
//$thisextension = strtolower( "com_whatever" );
//$thisextensionname = substr ( $thisextension, 4 );

JLoader::import( 'stratum.library.installer', JPATH_SITE . '/libraries' );
$stratuminstaller = new stratumInstaller();
$stratuminstaller->thisextension = $thisextension;
$stratuminstaller->manifest = !empty($this->manifest) ? $this->manifest : $stratuminstaller->getComponentManifestFile($thisextension);

//TODO: LOAD STRATUM LANGUAGE?
// load the component language file
$language = JFactory::getLanguage();
$language->load( $thisextension );

$status = new JObject();
$status->modules = array();
$status->plugins = array();
$status->templates = array();

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* // TEMPLATES UNINSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$templates = $stratuminstaller->getElementByPath('templates');
if ( (is_a($templates, 'JSimpleXMLElement') || is_a( $templates, 'JXMLElement')) && !empty( $templates ) && count($templates->children())) {

    foreach ($templates->children() as $template)
    {
        $mname		= $stratuminstaller->getAttribute('template', $template);
        $mpublish	= $stratuminstaller->getAttribute('publish', $template);
        $mclient	= JApplicationHelper::getClientInfo($stratuminstaller->getAttribute('client', $template), true);

        $package    = array();
        $package['type'] = 'template';
        $package['group'] = '';
        $package['element'] = $mname;
        $package['client'] = $stratuminstaller->getAttribute('client', $template);
        
        /*
         * fire the stratumInstaller with the foldername and folder entryType
        */
        $stratumInstaller = new stratumInstaller();
        $result = $stratumInstaller->uninstallExtension($pathToFolder, 'folder');

        // track the message and status of installation from stratumInstaller
        if ($result)
        {
            $alt = JText::_( "LIB_STRATUM_UNINSTALLED" );
            $mstatus = "<img src='" . Stratum::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
        } else {
            $alt = JText::_( "LIB_STRATUM_FAILED" );
            $error = $stratumInstaller->getError();
            $mstatus = "<img src='" . Stratum::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' />";
            $mstatus .= " - ".$error;
        }

        $status->templates[] = array('name'=>$mname,'client'=>$mclient->name, 'status'=>$mstatus );
    }
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * MODULE UNINSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

$modules = $stratuminstaller->getElementByPath('modules');
if ( (is_a($modules, 'JSimpleXMLElement') || is_a( $modules, 'JXMLElement')) && !empty( $modules ) && count($modules->children())) {

    foreach ($modules->children() as $module)
    {
		$mname		= $stratuminstaller->getAttribute('module', $module);
		$mpublish	= $stratuminstaller->getAttribute('publish', $module);
		$mposition	= $stratuminstaller->getAttribute('position', $module);
		$mclient	= JApplicationHelper::getClientInfo($stratuminstaller->getAttribute('client', $module), true);
                
        $package    = array();
        $package['type'] = 'module';
        $package['group'] = '';
        $package['element'] = str_replace('modules/', '', $mname);
        $package['client'] = $stratuminstaller->getAttribute('client', $module);
                
        /*
         * fire the stratumInstaller
         */
        $stratumInstaller = new stratumInstaller();
        $result = $stratumInstaller->uninstallExtension($package);
        
        // track the message and status of installation from stratumInstaller
        if ($result) 
        {
            $alt = JText::_( "LIB_STRATUM_UNINSTALLED" );
            $mstatus = "<img src='" . Stratum::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
        } 
            else 
        {
            $alt = JText::_( "LIB_STRATUM_FAILED" );
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
if ( (is_a($plugins, 'JSimpleXMLElement') || is_a( $plugins, 'JXMLElement')) && !empty( $plugins ) && count($plugins->children())) {

    foreach ($plugins->children() as $plugin)
    {
        $pname		= $stratuminstaller->getAttribute('plugin', $plugin);
        $ppublish	= $stratuminstaller->getAttribute('publish', $plugin);
        $pgroup		= $stratuminstaller->getAttribute('group', $plugin);
        $name		= $stratuminstaller->getAttribute('element', $plugin);

        $package    = array();
        $package['type'] = 'plugin';
        $package['group'] = $pgroup;
        $package['element'] = $name;
        $package['client'] = '';
        
        /*
         * fire the stratumInstaller
         */
        $stratumInstaller = new stratumInstaller();
        $result = $stratumInstaller->uninstallExtension($package);
        
        // track the message and status of installation from stratumInstaller
        if ($result) 
        {
            $alt = JText::_( "LIB_STRATUM_UNINSTALLED" );
            $pstatus = "<img src='" . Stratum::getURL( 'images' ) . "tick.png' border='0' alt='{$alt}' />";
        } 
            else 
        {
            $alt = JText::_( "LIB_STRATUM_FAILED" );
            $error = $stratumInstaller->getError();
            $pstatus = "<img src='" . Stratum::getURL( 'images' ) . "publish_x.png' border='0' alt='{$alt}' /> ";
            $pstatus .= " - ".$error;   
        }

        $status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'status'=>$pstatus);
    }
}


/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
 $rows = 0;
?>

<h2><?php echo JText::_('LIB_STRATUM_UNINSTALLATION_RESULTS'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('LIB_STRATUM_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('LIB_STRATUM_STATUS'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo JText::_('LIB_STRATUM_COMPONENT'); ?></td>
			<td><center><strong><?php echo JText::_('LIB_STRATUM_REMOVED'); ?></strong></center></td>
		</tr>
<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('LIB_STRATUM_MODULE'); ?></th>
			<th><?php echo JText::_('LIB_STRATUM_CLIENT'); ?></th>
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
			<th><?php echo JText::_('LIB_STRATUM_PLUGIN'); ?></th>
			<th><?php echo JText::_('LIB_STRATUM_GROUP'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td class="key"><center><?php echo $plugin['status']; ?></center></td>
		</tr>
	<?php endforeach;
endif; ?>
	</tbody>
</table>