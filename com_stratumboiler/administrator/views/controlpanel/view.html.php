<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_stratumboiler
 *
 * @copyright   Copyright (C) 2024 StratumBoiler Developer. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * Control Panel View
 */
class StratumBoilerViewControlPanel extends HtmlView
{
    protected \$items;
    protected \$pagination;
    protected \$state;

    /**
     * Display the view
     *
     * @param   string  \$tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display(\$tpl = null)
    {
        // We are not displaying any data from a model in this basic view,
        // but if we were, it would be fetched here.
        // Example:
        // $this->items        = $this->get('Items');
        // $this->pagination   = $this->get('Pagination');
        // $this->state        = $this->get('State');

        // Check for errors.
        if (count(\$errors = $this->get('Errors')))
        {
            throw new \Exception(implode("\n", \$errors), 500);
        }

        $this->addToolbar();
        parent::display(\$tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     * @since   1.6
     */
    protected function addToolbar()
    {
        \$user = Factory::getApplication()->getIdentity();

        // Set title
        ToolbarHelper::title(Text::_('COM_STRATUMBOILER_CONTROL_PANEL'), 'cogs'); // 'cogs' is an icon class

        // Add options button if user has configure permission.
        if (\$user->authorise('core.admin', 'com_stratumboiler') || \$user->authorise('core.options', 'com_stratumboiler'))
        {
            ToolbarHelper::preferences('com_stratumboiler');
        }
    }
}
