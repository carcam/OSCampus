<?php
/**
 * @package   com_oscampus
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2015-2016 Open Source Training, LLC. All rights reserved
 * @license
 */

use Oscampus\String;

defined('_JEXEC') or die();


abstract class OscampusViewList extends OscampusViewAdmin
{
    /**
     * @var OscampusModelAdminList
     */
    protected $model = null;

    /**
     * @var JForm
     */
    public $filterForm = null;

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

        $this->model = $this->getModel();

        $state = $this->model->getState();

        $this->setVariable('items', $this->model->getItems());
        $this->setVariable('pagination', $this->model->getPagination());

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

        $this->filterForm    = $this->model->getFilterForm();
    }

    /**
     * Set the standard ordering object values for use in Twig
     *
     * @param string $field
     * @param string $prefix
     * @param bool   $enabled
     *
     * @return void
     */
    protected function setOrdering($field = null, $prefix = null, $enabled = false)
    {
        $ordering = array_merge(
            $this->getVariable('ordering', array()),
            array(
                'enabled' => $enabled,
                'field'   => $field,
                'prefix'  => $prefix
            )
        );

        $this->setVariable('ordering', $ordering);
    }

    /**
     * Complement to setOrdering()
     *
     * @return object
     */
    protected function getOrdering()
    {
        return $this->getVariable('ordering');
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
