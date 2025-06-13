<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stratumboiler
 *
 * @copyright   Copyright (C) 2024 StratumBoiler Developer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Assuming StratumControllerAdmin is available via autoloader or included via lib_dscfork
// If StratumControllerAdmin is namespaced, a 'use' statement would be needed here.
// e.g., use LibDscfork\Library\Controller\StratumControllerAdmin;

/**
 * StratumBoiler ControlPanel Controller
 *
 * @since  1.0.0
 */
class StratumBoilerControllerControlPanel extends StratumControllerAdmin // StratumControllerAdmin from lib_dscfork
{
    /**
     * The default view for the display task.
     *
     * @var    string
     * @since  1.0.0
     */
    protected \$default_view = 'controlpanel';

    /**
     * Constructor.
     *
     * @param   array  \$config  An optional associative array of configuration settings.
     *
     * @see     JControllerLegacy
     * @since   1.0.0
     */
    public function __construct(\$config = [])
    {
        parent::__construct(\$config);
        // Additional constructor tasks can go here.
    }

    // Add any specific tasks for the controlpanel view here.
    // For a basic boilerplate, often no custom tasks are needed initially for the main control panel.
    // The display task is handled by the parent controller (BaseController -> StratumController -> StratumControllerAdmin).
}
