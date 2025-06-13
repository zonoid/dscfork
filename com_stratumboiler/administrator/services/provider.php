<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stratumboiler
 *
 * @copyright   Copyright (C) 2024 StratumBoiler Developer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The service provider for the StratumBoiler component (administrator).
 *
 * @since  1.0.0
 */
return new class implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * @param   Container  \$container  The DI container.
     *
     * @return  void
     * @since   1.0.0
     */
    public function register(Container \$container)
    {
        // Register the MVC Factory service
        \$container->set(
            MVCFactoryInterface::class,
            function (Container \$container)
            {
                \$component = \$container->get(ComponentInterface::class);
                \$mvcFactory = new MVCFactory(\$container->get('app'), \$component->getNamespace());

                // Use our own controller prefix if controllers are not in 'Controller' sub-namespace
                // \$mvcFactory->setControllerPrefix('StratumBoilerController'); // Example if classes are StratumBoilerControllerTask

                return \$mvcFactory;
            }
        );

        // Register the Component Dispatcher Factory service
        // This will automatically look for controllers in the 'Controller' sub-namespace
        // (e.g., StratumBoiler\Component\StratumBoiler\Administrator\Controller\ControlPanelController)
        // The manifest XML uses <namespace>StratumBoiler\Component\StratumBoiler</namespace>
        // So, for admin, it becomes StratumBoiler\Component\StratumBoiler\Administrator
        \$container->registerServiceProvider(new ComponentDispatcherFactory('#com_stratumboiler.administrator'));
        \$container->registerServiceProvider(new MVCFactory('#com_stratumboiler.administrator'));

        // If using the Stratum library's controllers directly (which are not namespaced under StratumBoiler),
        // a custom controller factory or dispatcher might be needed to locate them if they are not
        // named conventionally (e.g., StratumBoilerControllerControlPanel extending StratumControllerAdmin).
        // For now, assume conventional naming will be resolved by MVCFactory.
        // The class created was `StratumBoilerControllerControlPanel` in `controllers/controlpanel.php`.
        // The namespace will be `StratumBoiler\Component\StratumBoiler\Administrator\Controller\StratumBoilerControllerControlPanel`
        // if PSR-4 is set up for StratumBoiler\Component\StratumBoiler pointing to the component root.
        // The manifest uses <namespace>StratumBoiler\Component\StratumBoiler</namespace>.
        // The controllers folder contains `controlpanel.php` with class `StratumBoilerControllerControlPanel`.
        // The MVCFactory will look for `StratumBoiler\Component\StratumBoiler\Administrator\Controller\ControlpanelController`.
        // We might need to adjust the class name in `controllers/controlpanel.php` to `ControlpanelController.php`
        // and class `ControlpanelController` for it to be found automatically by the standard MVCFactory.
        // Or, we can add a specific controller service.

        // For simplicity with current controller `StratumBoilerControllerControlPanel`:
        // Add specific controller to the container if needed, or ensure class names match expectations.
        // Example for specific controller registration (if default dispatcher doesn't find it):
        /*
        \$container->set(
            'controller.controlpanel',
            function (Container \$container)
            {
                // Ensure StratumControllerAdmin is loaded if not namespaced/autoloaded
                // require_once JPATH_LIBRARIES . '/lib_dscfork/library/controller/admin.php';
                // require_once JPATH_ADMINISTRATOR . '/components/com_stratumboiler/controllers/controlpanel.php';
                return new \StratumBoilerControllerControlPanel();
            }
        );
        */
        // For now, rely on ComponentDispatcherFactory and MVCFactory to find controllers by convention.
        // This might require renaming `StratumBoilerControllerControlPanel` to `ControlpanelController`
        // and the file `controlpanel.php` to `ControlpanelController.php`.
        // This subtask will only create the provider.php. File renaming can be a subsequent step if needed.
    }
};
