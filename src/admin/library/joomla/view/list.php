<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\String;

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
        $inflector = String\Inflector::getInstance();

        $plural   = $this->getName();
        $singular = $inflector->toSingular($plural);

        OscampusToolbarHelper::addNew($singular . '.add');
        OscampusToolbarHelper::editList($singular . '.edit');

        $table = $this->getModel()->getTable();
        if (array_key_exists('published', get_object_vars($table))) {
            OscampusToolbarHelper::publish($plural . '.publish', 'JTOOLBAR_PUBLISH', true);
            OscampusToolbarHelper::unpublish($plural . '.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        OscampusToolbarHelper::deleteList('COM_OSCAMPUS_DELETE_CONFIRM', $plural . '.delete');

        parent::setToolbar();
    }
}
