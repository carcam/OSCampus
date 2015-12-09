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

    protected function setup()
    {
        parent::setup();

        $model = $this->getModel();
        $state = $model->getState();

        $this->setVariable('list_order', $this->escape($state->get('list.ordering')));
        $this->setVariable('list_dir', $this->escape($state->get('list.direction')));
        $this->setVariable('items', $model->getItems());
        $this->setVariable('pagination', $model->getPagination());

        $ordering = array(
            'enabled'   => false,
            'field'     => null,
            'prefix'    => null,
            'order'     => $this->escape($state->get('list.ordering')),
            'direction' => $this->escape($state->get('list.direction'))
        );
        $this->setVariable('ordering', $ordering);


        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }
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

    /**
     * Return a group ID for determining if an item is at the top or bottom of a scoped sorting group
     *
     * @param object $item
     *
     * @return string
     */
    public function getSortGroupId($item)
    {
        return '';
    }

    /**
     * Determine if the selected item is at the top of a sorting group
     *
     * @param int   $index
     * @param array $items
     *
     * @return bool
     */
    public function isSortGroupTop($index, $items)
    {
        if ($index == 0) {
            return true;
        }

        if (isset($items[$index])) {
            $currentSortGroupId = $this->getSortGroupId($items[$index]);
            $lastSortGroupId    = isset($items[$index - 1]) ? $this->getSortGroupId($items[$index - 1]) : '';
            return $currentSortGroupId != $lastSortGroupId;
        }

        return false;
    }

    /**
     * Determine if the selected item is at the bottom of a sorting group
     *
     * @param int   $index
     * @param array $items
     *
     * @return bool
     */
    public function isSortGroupBottom($index, $items)
    {
        if (count($items) == ($index + 1)) {
            return true;
        }

        if (isset($items[$index])) {
            $currentSortGroupId = $this->getSortGroupId($items[$index]);
            $nextSortGroupId    = isset($items[$index + 1]) ? $this->getSortGroupId($items[$index + 1]) : '';
            return $currentSortGroupId != $nextSortGroupId;
        }

        return false;
    }
}
