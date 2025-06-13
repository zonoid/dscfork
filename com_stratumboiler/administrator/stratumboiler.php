<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stratumboiler
 *
 * @copyright   Copyright (C) 2024 StratumBoiler Developer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Dispatcher\ComponentDispatcherFactory;
use Joomla\CMS\Component\ComponentHelper;

\$app = Factory::getApplication();

if (!\$app instanceof CMSApplication)
{
    throw new \RuntimeException('Application is not a CMSApplication', 500);
}

// Get the component parameters
\$params = ComponentHelper::getParams('com_stratumboiler');

// Create and execute the dispatcher.
// The namespace comes from the manifest.
// The ComponentDispatcherFactory will look for services/provider.php.
\$dispatcher = (new ComponentDispatcherFactory(\$app))->createDispatcher('com_stratumboiler', 'StratumBoiler\\Component\\StratumBoiler');
\$dispatcher->dispatch();
