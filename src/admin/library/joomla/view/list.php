<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

defined('_JEXEC') or die();


abstract class OscampusViewList extends OscampusViewAdmin
{
    /**
     * Default admin screen title
     *
     * @param string $sub
     * @param string $icon
     *
     * @return void
     */
    protected function setTitle($sub = null, $icon = 'oscampus')
    {
        $name = strtoupper($this->getName());

        parent::setTitle('COM_OSCAMPUS_SUBMENU_' . $name, $icon);
    }

    /**
     * Method to display the view
     *
     * @param  string $tpl The name of the template file to parse
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $state = $this->get('State');

        $this->setVariable('list_order', $this->escape($state->get('list.ordering')));
        $this->setVariable('list_dir', $this->escape($state->get('list.direction')));
        $this->setVariable('items', $this->get('Items'));
        $this->setVariable('pagination', $this->get('Pagination')->getListFooter());

        // $this->filterForm    = $this->get('FilterForm');
        // $this->activeFilters = $this->get('ActiveFilters');

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        // $notices = OscampusHelper::getNotices();
        // OscampusHelper::enqueueMessages($notices);

        parent::display($tpl);
    }

    /**
     * Method to set default buttons to the toolbar
     *
     * @return  void
     */
    protected function setToolbar()
    {
        $controller = $this->getName();
        $controllerPlural = JStringInflector::getInstance(true)->toPlural($name);

        OscampusToolbarHelper::addNew($controller . '.add');
        OscampusToolbarHelper::editList($controller . '.edit');
        OscampusToolbarHelper::deleteList('COM_OSCAMPUS_DELETE_CONFIRM', $controllerPlural . '.delete');

        parent::setToolbar();
    }
}
