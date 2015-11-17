<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Joomla\String;

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
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $model = $this->getModel();
        $state = $model->getState();

        $this->setVariable('list_order', $this->escape($state->get('list.ordering')));
        $this->setVariable('list_dir', $this->escape($state->get('list.direction')));
        $this->setVariable('items', $model->getItems());
        $this->setVariable('pagination', $model->getPagination()->getListFooter());

        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        parent::display($tpl);
    }

    /**
     * Method to set default buttons to the toolbar
     *
     * @return  void
     */
    protected function setToolbar()
    {
        $controller       = $this->getName();
        $controllerPlural = String\Inflector::getInstance(true)->toPlural($controller);

        OscampusToolbarHelper::addNew($controller . '.add');
        OscampusToolbarHelper::editList($controller . '.edit');

        $table = $this->getModel()->getTable();
        if (array_key_exists('published', get_object_vars($table))) {
            OscampusToolbarHelper::publish($controllerPlural . '.publish', 'JTOOLBAR_PUBLISH', true);
            OscampusToolbarHelper::unpublish($controllerPlural . '.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        OscampusToolbarHelper::deleteList('COM_OSCAMPUS_DELETE_CONFIRM', $controllerPlural . '.delete');

        parent::setToolbar();
    }
}